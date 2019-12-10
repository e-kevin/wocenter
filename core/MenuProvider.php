<?php

namespace wocenter\core;

use wocenter\db\ActiveRecord;
use wocenter\helpers\ArrayHelper;
use wocenter\interfaces\MenuProviderInterface;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;

/**
 * 菜单配置提供者实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class MenuProvider extends BaseObject implements MenuProviderInterface
{
    
    /**
     * @var string 系统扩展模块ID
     */
    public $extensionId = 'extension';
    
    /**
     * @var ActiveRecord
     */
    private $_model;
    
    /**
     * 菜单配置数据
     *
     * @var array
     */
    private $_menuConfig;
    
    private $_all;
    
    private $_allGroupByLevel;
    
    /**
     * @inheritdoc
     */
    public function getAll($level = null): array
    {
        if (null !== $level) {
            if (null === $this->_allGroupByLevel) {
                $this->_allGroupByLevel = ArrayHelper::index(
                    $this->_getAll(),
                    'id',
                    'level'
                );
            }
            
            return $this->_allGroupByLevel[$level] ?? [];
        }
        
        return $this->_getAll();
    }
    
    /**
     * 获取所有菜单数据
     *
     * @return array
     */
    protected function _getAll()
    {
        if (null === $this->_all) {
            if (empty($this->getMenuConfig())) {
                return $this->_all = [];
            }
            $id = 0;
            foreach ($this->getMenuConfig() as $app => $row) {
                $this->_all = array_merge($this->_all ?? [], $this->_formatMenuConfig($row, $id, 0));
            }
            $this->_all = ArrayHelper::index($this->_all, 'id');
        }
        
        return $this->_all;
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        $this->_all = $this->_allGroupByLevel = null;
    }
    
    /**
     * @inheritdoc
     */
    public function getModel()
    {
        if (null === $this->_model) {
            $this->setModel();
        }
        
        return $this->_model;
    }
    
    /**
     * @inheritdoc
     */
    public function setModel($config = [])
    {
        if (!isset($config['class'])) {
            throw new InvalidConfigException('The `$model` property must contain the `class` key name.');
        }
        $this->_model = Yii::createObject($config);
    }
    
    /**
     * @inheritdoc
     */
    public function getMenuConfig()
    {
        if (null === $this->_menuConfig) {
            $this->setMenuConfig();
        }
        
        return $this->_menuConfig;
    }
    
    /**
     * @inheritdoc
     */
    public function setMenuConfig($config = [])
    {
        foreach ($config ?: $this->defaultMenuConfig() as $app => $row) {
            $level = 1;
            $this->_menuConfig[$app] = $this->_initMenu($row, $app, $level);
        }
    }
    
    /**
     * 默认菜单配置，注意字段类型
     *
     * 菜单配置数组里，可用的键名包含以下：
     *  - `label` string: 菜单名称，建议简写，如‘列表’、‘更新’、‘删除’等，通常在某些地方需要显示很多菜单项时使用，
     * 如系统的权限配置里。
     *  - `alias_name` string: 菜单别名，建议完整显示，如‘用户列表’、‘更新用户’等。
     *  - `icon_html` string: 菜单图标
     *  - `modularity` string: 菜单所属的模块，一般为当前模块，不填写系统则会自动从`url`里提取出菜单所属的模块。
     * 如果`url`为空或不是规则的路由地址，如系统默认为父级菜单赋值的`#`情况时，必须明确该菜单所属模块，
     * 否则系统不会同步更新到菜单数据库里。
     *  - `url` string: url地址，可以是系统已经配置了的路由映射地址，默认为`#`
     *  - `params` array: url地址参数
     *  - `menu_config` array: 菜单配置数据，一般用于小部件时，为小部件提供菜单本身所需的配置参数
     *  - `show_on_menu` boolean: 是否显示该菜单，默认不显示
     *  - `description` string: 菜单描述
     *  - `sort_order` int: 排序
     *  - `items` array: 子类菜单配置数组
     *  - `theme` string: 菜单所属的主题，默认为'common'，该值为系统保留字段，建议开发者避免使用该值作为主题名
     *
     * @return array
     */
    protected function defaultMenuConfig(): array
    {
        return [
            'backend' => [
                [
                    'label' => '扩展中心',
                    'icon_html' => 'cube',
                    'modularity' => $this->extensionId,
                    'show_on_menu' => true,
                    'sort_order' => 1005,
                    'items' => [
                        // 扩展管理
                        [
                            'label' => '扩展管理',
                            'icon_html' => 'cubes',
                            'modularity' => $this->extensionId,
                            'show_on_menu' => true,
                            'items' => [
                                [
                                    'label' => '模块管理',
                                    'url' => "/{$this->extensionId}/module/index",
                                    'description' => '模块管理',
                                    'show_on_menu' => true,
                                    'items' => [
                                        [
                                            'label' => '安装',
                                            'alias_name' => '安装模块扩展',
                                            'url' => "/{$this->extensionId}/module/install",
                                        ],
                                        [
                                            'label' => '管理',
                                            'alias_name' => '管理模块扩展',
                                            'url' => "/{$this->extensionId}/module/update",
                                        ],
                                    ],
                                ],
                                [
                                    'label' => '控制器管理',
                                    'url' => "/{$this->extensionId}/controller/index",
                                    'description' => '控制器管理',
                                    'show_on_menu' => true,
                                    'items' => [
                                        [
                                            'label' => '安装',
                                            'alias_name' => '安装控制器扩展',
                                            'url' => "/{$this->extensionId}/controller/install",
                                        ],
                                        [
                                            'label' => '管理',
                                            'alias_name' => '管理控制器扩展',
                                            'url' => "/{$this->extensionId}/controller/update",
                                        ],
                                    ],
                                ],
                                [
                                    'label' => '主题管理',
                                    'url' => "/{$this->extensionId}/theme/index",
                                    'description' => '主题管理',
                                    'show_on_menu' => true,
                                    'items' => [
                                        [
                                            'label' => '安装',
                                            'alias_name' => '安装主题扩展',
                                            'url' => "/{$this->extensionId}/theme/install",
                                        ],
                                        [
                                            'label' => '管理',
                                            'alias_name' => '管理主题扩展',
                                            'url' => "/{$this->extensionId}/theme/update",
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'label' => '扩展功能',
                            'icon_html' => 'cogs',
                            'modularity' => $this->extensionId,
                            'show_on_menu' => true,
                            'items' => [
                                [
                                    'label' => '同步',
                                    'alias_name' => '同步菜单',
                                    'url' => "/{$this->extensionId}/functions/sync-menu",
                                    'description' => '同步扩展菜单',
                                    'show_on_menu' => true,
                                    'params' => [
                                        'name' => 'value',
                                    ],
                                    'menu_config' => [
                                        'linkOptions' => [
                                            'data-method' => 'post',
                                            'data-pjax' => 1,
                                        ],
                                    ],
                                ],
                                [
                                    'label' => '清理',
                                    'alias_name' => '清理缓存',
                                    'url' => "/{$this->extensionId}/functions/clear-cache",
                                    'description' => '清理扩展缓存',
                                    'show_on_menu' => true,
                                    'menu_config' => [
                                        'linkOptions' => [
                                            'data-method' => 'post',
                                            'data-pjax' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'frontend' => [],
        ];
    }
    
    /**
     * 格式化菜单配置数据为一元数组，并为菜单配置数据虚构`$id`和`$parent_id`值
     *
     * @param array $items
     * @param int $id
     * @param int $parent_id
     *
     * @return array
     */
    private function _formatMenuConfig($items, &$id, $parent_id)
    {
        foreach ($items as $key => &$item) {
            $item['id'] = ++$id;
            $item['parent_id'] = $parent_id;
            if (isset($item['items'])) {
                $item['items'] = $this->_formatMenuConfig($item['items'], $id, $id);
            }
        }
        
        return ArrayHelper::treeToList($items);
    }
    
    /**
     * 初始化菜单
     *
     * @param array $items
     * @param string $app
     * @param int $level
     *
     * @return array
     */
    private function _initMenu($items, $app, $level)
    {
        foreach ($items as $key => $item) {
            $this->initMenuData($item, $app, $level);
            if (isset($item['items'])) {
                // 当父类指定某个主题后，则子类也归属该主题
                $item['items'] = $this->_initMenu($item['items'], $app, $level + 1);
            }
            // 转换菜单数据键值，便于使用\yii\helpers\ArrayHelper::merge合并相同键名的数组到同一分组下
            $uniqueKey = $item['theme'] . '/' . $item['label'];
            $items[$uniqueKey] = ArrayHelper::merge($items[$uniqueKey] ?? [], $item);
            unset($items[$key]);
        }
        
        return $items;
    }
    
    /**
     * 初始化菜单配置数据
     * 补全修正菜单数组，确保可用字段存在于菜单数据表里
     *
     * @param array $item
     * @param string $app
     * @param int $level
     *
     * @throws InvalidConfigException
     */
    protected function initMenuData(&$item = [], $app, $level)
    {
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        foreach (['params', 'menu_config'] as $field) {
            $item[$field] = $item[$field] ?? [];
            if (!is_array($item[$field])) {
                throw new InvalidConfigException("Unsupported type for \"$field\": " . gettype($item[$field]) .
                    "\n" . VarDumper::dumpAsString($item));
            }
        }
        $item['alias_name'] = $item['alias_name'] ?? $item['label'];
        $item['category_id'] = $app; // 为菜单项添加分类ID
        $item['url'] = $item['url'] ?? '#';
        // 模块ID
        if (!isset($item['modularity']) && $item['url'] != '#') {
            preg_match('/\w+/', $item['url'], $modularity);
            $item['modularity'] = $modularity[0];
        }
        $item['created_type'] = $item['created_type'] ?? MenuProviderInterface::CREATE_TYPE_BY_EXTENSION;
        $item['show_on_menu'] = isset($item['show_on_menu']) ? intval($item['show_on_menu']) : 0;
        $item['sort_order'] = $item['sort_order'] ?? 0;
        $item['status'] = isset($item['status']) ? intval($item['status']) : 1;
        $item['theme'] = $item['theme'] ?? 'common';
        // 需要补全空字符串的字段
        $fields = ['icon_html', 'description'];
        foreach ($fields as $field) {
            if (!isset($item[$field])) {
                $item[$field] = '';
            }
        }
        $item['level'] = $level; // 虚拟菜单层级数据，用于定位菜单
    }
    
}
