<?php
namespace wocenter\services;

use wocenter\backend\modules\menu\models\Menu;
use wocenter\core\Service;
use wocenter\interfaces\ModularityInfoInterface;
use wocenter\Wc;
use wocenter\helpers\ArrayHelper;
use Yii;

/**
 * 菜单服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class MenuService extends Service
{

    /**
     * @var string|array|callable|Menu 菜单类
     */
    public $menuModel = '\wocenter\backend\modules\menu\models\Menu';

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'menu';
    }

    /**
     * 根据查询条件获取指定（单个或多个）分类的菜单数据
     *
     * @param string|array $category 分类ID
     * @param array $condition 查询条件
     * @param integer|boolean $duration 缓存时间
     *
     * @return array 如果$category分类ID为字符串，则返回该分类的一维数组，否则返回二维数组['backend' => [], 'frontend' => [], 'main' => []]
     */
    public function getMenus($category = '', array $condition = [], $duration = 60)
    {
        $menus = $this->getMenusByCategoryWithFilter($category, $condition, false, $duration);
        if (is_string($category)) {
            return isset($menus[$category]) ? $menus[$category] : [];
        } else {
            return $menus;
        }
    }

    /**
     * 根据查询条件和$filterCategory指标 [获取 || 不获取] 指定分类（单个或多个）的菜单数据
     *
     * @param string|array $category 分类ID
     * @param array $condition 查询条件
     * @param boolean $filterCategory 过滤指定$category分类的菜单，默认：不过滤
     * @param integer|boolean $duration 缓存时间
     *
     * @return array ['backend' => [], 'frontend' => [], 'main' => []]
     */
    public function getMenusByCategoryWithFilter($category = '', array $condition = [], $filterCategory = false, $duration = 60)
    {
        /** @var Menu $menuModel */
        $menuModel = Yii::createObject($this->menuModel);
        $menus = ArrayHelper::listSearch(
            $menuModel->getAll($duration),
            array_merge([
                'category_id' => [
                    $filterCategory ?
                        (is_array($category) ? 'not in' : 'neq') :
                        (is_array($category) ? 'in' : 'eq'),
                    $category,
                ],
            ], $condition)
        );

        return $menus ? ArrayHelper::index($menus, 'id', 'category_id') : [];
    }

    /**
     * 同步所有已安装模块的菜单项，此处不获取缓存中的菜单项
     *
     * @return boolean
     */
    public function syncMenus()
    {
        /** @var Menu $menuModel */
        $menuModel = $this->menuModel;
        // 获取已经安装的模块菜单配置信息
        $allInstalledMenuConfig = $this->_getMenuConfigs();
        // 获取数据库里的所有模块菜单数据，不包括用户自建数据
        $menuInDatabase = $this->getMenus('backend', [
            'created_type' => $menuModel::CREATE_TYPE_BY_MODULE
        ], false);
        $updateDbMenus = $this->_convertMenuData2Db($allInstalledMenuConfig, 0, $menuInDatabase);
        $this->_fixMenuData($menuInDatabase, $updateDbMenus);

        // 操作数据库
        $this->_updateMenus($updateDbMenus);
        // 删除菜单缓存
        $this->clearCache();

        return true;
    }

    /**
     * 获取所有已安装模块的菜单配置数据
     *
     * @param boolean $treeToList 是否把树型结构数组转换为一维数组，默认为`false`，不转换
     *
     * @return array
     */
    protected function _getMenuConfigs($treeToList = false)
    {
        $arr = [];
        // 获取所有已经安装的模块配置文件
        foreach (Wc::$service->getModularity()->getInstalledModules() as $moduleId => $row) {
            /* @var $infoInstance ModularityInfoInterface */
            $infoInstance = $row['infoInstance'];
            $arr = ArrayHelper::merge($arr, $this->_formatMenuConfig($infoInstance->getMenus()));
        }

        return $treeToList ? ArrayHelper::treeToList($arr, 'items') : $arr;
    }

    /**
     * 格式化菜单配置数据，主要把键值`name`转换成键名，方便使用\yii\helpers\ArrayHelper::merge合并相同键名的数组到同一分组下
     *
     * @param array $menus 菜单数据
     *
     * @return array
     */
    protected function _formatMenuConfig($menus)
    {
        $arr = [];
        if (empty($menus)) {
            return $arr;
        }
        foreach ($menus as $key => $menu) {
            $key = isset($menu['name']) ? $menu['name'] : $key;
            $arr[$key] = $menu;
            if (isset($menu['items'])) {
                $arr[$key]['items'] = $this->_formatMenuConfig($menu['items']);
            }
        }

        return $arr;
    }

    /**
     * 初始化菜单配置数据，用于补全修正菜单数组。可用字段必须存在于$this->menuModel数据表里
     *
     * @param array $menus
     */
    protected function _initMenuConfig(&$menus = [])
    {
        $menus['url'] = isset($menus['url']) ? $menus['url'] : 'javascript:;';
        $menus['full_url'] = isset($menus['full_url']) ? $menus['full_url'] : $menus['url'];
        $menus['params'] = isset($menus['params']) ? serialize($menus['params']) : '';
        // 模块ID
        if (!isset($menus['modularity']) && $menus['full_url'] != 'javascript:;') {
            preg_match('/\w+/', $menus['full_url'], $modularity);
            $menus['modularity'] = $modularity[0];
        }
        $menus['category_id'] = Yii::$app->id;
        $menus['created_type'] = Menu::CREATE_TYPE_BY_MODULE;
        $menus['show_on_menu'] = isset($menus['show_on_menu']) ? 1 : 0;
        $menus['alias_name'] = isset($menus['alias_name']) ? $menus['alias_name'] : $menus['name'];
        // 需要补全的字段
        $fields = ['icon_html', 'description'];
        foreach ($fields as $field) {
            if (!isset($menus[$field])) {
                $menus[$field] = '';
            }
        }
    }

    /**
     * 转换菜单数据，用以插入数据库
     *
     * @param array $menus 需要转换的菜单配置信息
     * @param integer $parentId 数组父级ID
     * @param array &$menuInDatabase 数据库里的菜单数据
     *
     * @return array ['create', 'update']
     * @throws \yii\db\Exception
     */
    protected function _convertMenuData2Db(array $menus, $parentId = 0, &$menuInDatabase = [])
    {
        if (empty($menus)) {
            return [];
        }
        $arr = [];

        /** @var Menu $menuModel */
        $menuModel = $this->menuModel;
        foreach ($menus as $row) {
            $this->_initMenuConfig($row);

            // 排除没有设置归属模块的数据以及中断该数据的子数据
            // todo 改为系统日志记录该错误或抛出系统异常便于更正?
            if (empty($row['modularity'])) {
                continue;
            }

            $items = ArrayHelper::remove($row, 'items', []);
            $row['parent_id'] = $parentId;
            $arr['menuConfig'][] = $row;
            $condition = [
                'name' => $row['name'],
                'modularity' => $row['modularity'],
                'full_url' => $row['full_url'],
                'parent_id' => $row['parent_id'],
            ];

            if (!empty($items) // 存在子级菜单配置数据
                || $row['parent_id'] == 0 // 菜单为顶级菜单
            ) {
                // 数据库里存在数据
                if (($data = ArrayHelper::listSearch($menuInDatabase, $condition, true))) {
                    // 检测数据是否改变
                    foreach ($row as $key => $value) {
                        if ($data[0][$key] != $value) {
                            $arr['update'][$data[0]['id']][$key] = $value;
                        }
                    }
                    $arr = ArrayHelper::merge($arr, $this->_convertMenuData2Db($items, $data[0]['id'], $menuInDatabase));
                } else {
                    // 不存在父级菜单则递归新建父级菜单
                    if (Yii::$app->getDb()->createCommand()->insert($menuModel::tableName(), $row)->execute()) {
                        $find = $menuModel::find()->where($row)->asArray()->one();
                        // 同步更新数据库已有数据
                        $menuInDatabase[] = $find;
                        $arr = ArrayHelper::merge($arr, $this->_convertMenuData2Db($items, $find['id'], $menuInDatabase));
                    }
                }
            }

            // 数据库里存在数据
            if (
                ($data = ArrayHelper::listSearch($menuInDatabase, $condition, true)) ||
                // 最底层菜单可以修改`name`字段
                ($data = ArrayHelper::listSearch($menuInDatabase, [
                    'modularity' => $row['modularity'],
                    'full_url' => $row['full_url'],
                    'parent_id' => $row['parent_id'],
                ], true))
            ) {
                // 检测数据是否改变
                foreach ($row as $key => $value) {
                    if ($data[0][$key] != $value) {
                        $arr['update'][$data[0]['id']][$key] = $value;
                    }
                }
            } else {
                // 排序，便于批量插入数据库
                ksort($row);
                $arr['create'][] = $row;
                // 同步更新数据库已有数据
                $menuInDatabase[] = $row;
            }
        }

        return $arr;
    }

    /**
     * 对比数据库已有数据，修正待写入数据库的菜单数据
     *
     * @param array $menuInDatabase 数据库里的菜单数据
     * @param array $arr 待处理数组 ['create', 'update']
     */
    private function _fixMenuData($menuInDatabase = [], &$arr = [])
    {
        foreach ($menuInDatabase as $row) {
            // 配置数据里已删除，则删除数据库对应数据
            if (
                !ArrayHelper::listSearch($arr['menuConfig'], [
                    'name' => $row['name'],
                    'modularity' => $row['modularity'],
                    'full_url' => $row['full_url'],
                ], true) &&
                (!key_exists($row['id'], isset($arr['update']) ? $arr['update'] : []))
            ) {
                $arr['delete'][$row['id']] = $row['id'];
            }
        }
    }

    /**
     * 更新所有模块菜单
     *
     * @param array $array 需要操作的数据 ['delete', 'create', 'update']
     */
    private function _updateMenus($array)
    {
        /** @var Menu $menuModel */
        $menuModel = $this->menuModel;
        if (!empty($array['delete'])) {
            Yii::$app->getDb()->createCommand()->delete($menuModel::tableName(), ['id' => $array['delete']])
                ->execute();
        }
        if (!empty($array['create'])) {
            Yii::$app->getDb()->createCommand()->batchInsert($menuModel::tableName(), array_keys($array['create'][0]), $array['create'])
                ->execute();
        }
        if (!empty($array['update'])) {
            foreach ($array['update'] as $id => $row) {
                Yii::$app->getDb()->createCommand()->update($menuModel::tableName(), $row, ['id' => $id])
                    ->execute();
            }
        }
    }

    /**
     * 删除缓存
     */
    public function clearCache()
    {
        /** @var Menu $menuModel */
        $menuModel = Yii::createObject($this->menuModel);
        $menuModel->clearCache();
    }

}
