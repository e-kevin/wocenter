<?php
namespace wocenter\services;

use wocenter\core\Service;
use wocenter\models\Module;
use wocenter\Wc;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 管理系统模块类
 *
 * @property string $coreModulePath 系统核心模块目录
 * @property string $developerModulePath 开发者模块目录
 * @property \wocenter\services\modularity\LoadService $load 加载模块配置服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ModularityService extends Service
{

    /**
     * 缓存已经安装的模块信息
     */
    const CACHE_INSTALLED_MODULES = 'installedModules';

    /**
     * 缓存未安装的模块信息
     */
    const CACHE_UNINSTALL_MODULES = 'uninstallModules';

    /**
     * 缓存所有模块信息
     */
    const CACHE_ALL_MODULE_FILES = 'allModuleFiles';

    /**
     * 缓存所有模块路由规则
     */
    const CACHE_MODULE_URL_RULE = 'allModuleUrlRule';

    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;

    /**
     * @var boolean 开启调试模式
     */
    public $debug = false;

    /**
     * @var array 调试模块。主要用于读取所需测试的模块目录，其他目录不加载
     */
    public $debugModules = ['account', 'menu', 'passport', 'modularity', 'system'];

    /**
     * @var array 核心模块，必须安装
     */
    public $coreModules = ['account', 'action', 'data', 'log', 'menu', 'modularity', 'notification', 'operate', 'passport', 'system'];

    /**
     * @var string|array|callable|Module 模块类
     */
    public $moduleModel = '\wocenter\models\Module';

    /**
     * @var string 系统核心模块命名空间，加载模块时系统会自动转换该命名空间为模块目录并搜索其下所有有效的模块
     * @see getCoreModulePath()
     */
    public $coreModuleNamespace = 'wocenter\backend\modules';

    /**
     * @var string 开发者模块命名空间，加载模块时系统会自动转换该命名空间为模块目录并搜索其下所有有效的模块
     * @see getDeveloperModulePath()
     */
    public $developerModuleNamespace = 'backend\modules';

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'modularity';
    }

    /**
     * 获取系统已经安装的模块名称，主要用于列表筛选
     *
     * todo:添加缓存
     *
     * @return array e.g. ['account' => '账户管理', 'rbac' => '权限管理']
     */
    public function getInstalledModuleSelectList()
    {
        return ArrayHelper::getColumn(
            $this->getInstalledModules(),
            'infoInstance.name'
        );
    }

    /**
     * 获取系统已经安装的模块信息，包含模块Info详情
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getInstalledModules()
    {
        /** @var Module $moduleModel */
        $moduleModel = Yii::createObject($this->moduleModel);
        $installedModule = $moduleModel->getInstalledModuleId();

        return $installedModule ? $this->getLoad()->getModuleConfig($installedModule) : [];
    }

    /**
     * 获取当前应用未安装的模块ID
     *
     * @return array 未安装的模块ID
     */
    public function getUninstalledModuleId()
    {
        return $this->getModulesInternal('uninstall');
    }

    /**
     * 获取已安装的模块配置信息，用于设置系统模块Yii::$app->setModule()
     *
     * @return array [
     * [
     *  $moduleId => 'moduleClass',
     * ]
     */
    public function getInstalledModuleConfigs()
    {
        return $this->getModulesInternal('install');
    }

    /**
     * 根据$type类型获取应用相关模块
     *
     * @param string $type 模块类型
     *
     * @return array
     */
    protected function getModulesInternal($type)
    {
        $appId = Yii::$app->id;
        switch ($type) {
            case 'install':
                $arr = Wc::getOrSet([$appId, self::CACHE_INSTALLED_MODULES], function () {
                    return ArrayHelper::getColumn(
                        $this->getInstalledModules(),
                        'moduleClass'
                    );
                }, $this->cacheDuration);
                break;
            case 'uninstall':
                $arr = Wc::getOrSet([$appId, self::CACHE_UNINSTALL_MODULES], function () {
                    /** @var Module $moduleModel */
                    $moduleModel = Yii::createObject($this->moduleModel);
                    // 已经安装的模块ID数组
                    $installedModuleIds = $moduleModel->getInstalledModuleId();
                    // 系统存在的模块ID数组
                    $existModuleIds = array_keys($this->getLoad()->getModuleConfig());

                    // 未安装的模块ID数组
                    return array_diff($existModuleIds, $installedModuleIds);
                }, $this->cacheDuration);
                break;
            default:
                throw new InvalidParamException('The "type" param must be set.');
        }

        return $arr;
    }

    /**
     * 获取所有模块信息，并以数据库里的配置信息为准，主要用于列表
     *
     * @return array
     */
    public function getModuleList()
    {
        /** @var Module $moduleModel */
        $moduleModel = $this->moduleModel;
        $dbModules = $moduleModel::find()->select('id,is_system')
            ->where(['app' => Yii::$app->id])
            ->indexBy('id')->asArray()->all();
        $allModules = $this->getLoad()->getModuleConfig();
        foreach ($allModules as $moduleId => &$v) {
            $v['id'] = $moduleId;
            // 数据库里存在模块信息则标识模块已安装
            if (array_key_exists($v['id'], $dbModules)) {
                $existModule = $dbModules[$v['id']];
                // 是否为系统模块
                $v['infoInstance']->isSystem =
                    $v['infoInstance']->isSystem
                    || $existModule['is_system']
                    || in_array($v['id'], $this->coreModules);
                // 系统模块不可卸载
                $v['infoInstance']->canUninstall = !$v['infoInstance']->isSystem;
            } else {
                // 数据库不存在数据则可以进行安装
                $v['infoInstance']->canInstall = true;
            }
        }

        return $allModules;
    }

    /**
     * 获取单个模块详情，主要用于管理和安装模块
     *
     * @param string $id 模块ID
     * @param boolean $onDataBase 获取数据库数据，默认获取，一般是用于更新模块信息
     *
     * @return Module
     * @throws NotFoundHttpException
     */
    public function getModuleInfo($id, $onDataBase = true)
    {
        $modules = $this->getLoad()->getModuleConfig();
        if ($modules[$id] == null) {
            throw new NotFoundHttpException('模块不存在');
        }
        if ($onDataBase) {
            /** @var Module $model */
            $model = $this->moduleModel;
            if (($model = $model::find()->where(['id' => $id, 'app' => Yii::$app->id])->one()) == null) {
                throw new NotFoundHttpException('模块暂未安装');
            }
            $model->infoInstance = $modules[$id]['infoInstance'];
            // 系统模块及必须安装的模块不可卸载
            $model->infoInstance->canUninstall = !$model->infoInstance->isSystem && !$model->is_system && !in_array($id, $this->coreModules);
        } else {
            /** @var Module $model */
            $model = new $this->moduleModel();
            $model->infoInstance = $modules[$id]['infoInstance'];
            $model->infoInstance->canInstall = true;
            $model->id = $id;
            $model->app = Yii::$app->id;
            $model->is_system = $model->infoInstance->isSystem ? 1 : 0;
        }

        return $model;
    }

    /**
     * 根据系统核心模块命名空间自动获取模块目录
     *
     * @return boolean|string
     */
    public function getCoreModulePath()
    {
        return Yii::getAlias('@' . str_replace('\\', '/', $this->coreModuleNamespace));
    }

    /**
     * 根据开发者模块命名空间自动获取模块目录
     *
     * @return boolean|string
     */
    public function getDeveloperModulePath()
    {
        return Yii::getAlias('@' . str_replace('\\', '/', $this->developerModuleNamespace));
    }

    /**
     * 加载模块配置服务类
     *
     * @return \wocenter\services\modularity\LoadService
     */
    public function getLoad()
    {
        return $this->getSubService('load');
    }

    /**
     * 删除缓存
     * - 删除所有模块缓存
     */
    public function clearCache()
    {
        $appId = Yii::$app->id;
        Yii::$app->getCache()->delete([$appId, self::CACHE_INSTALLED_MODULES]);
        Yii::$app->getCache()->delete([$appId, self::CACHE_UNINSTALL_MODULES]);
        Yii::$app->getCache()->delete([$appId, self::CACHE_ALL_MODULE_FILES]);
        Yii::$app->getCache()->delete([$appId, self::CACHE_MODULE_URL_RULE]);
    }

}
