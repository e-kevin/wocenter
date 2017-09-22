<?php
namespace wocenter\services;

use wocenter\core\Service;
use wocenter\backend\modules\modularity\models\Module;
use wocenter\Wc;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 管理系统模块类
 *
 * @property string $coreModuleNamespace 系统核心模块命名空间
 * @property string $developerModuleNamespace 开发者模块命名空间
 * @property array $appModuleNamespace 各应用默认的模块命名空间
 * @property array $coreModules 当前应用的核心模块
 *
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
     * 缓存所有模块信息，包括系统核心模块和开发者模块
     */
    const CACHE_ALL_MODULE_CONFIG = 'allModuleConfig';

    /**
     * 缓存所有已经安装的模块路由规则
     */
    const CACHE_MODULE_URL_RULE = 'allModuleUrlRule';

    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;

    /**
     * @var string|array|callable|Module 模块类
     */
    public $moduleModel = '\wocenter\backend\modules\modularity\models\Module';

    /**
     * @var boolean 开启调试模式
     */
    public $debug = false;

    /**
     * @var array 调试模块。主要用于读取所需测试的模块目录，其他目录不加载
     */
    public $debugModules = ['account', 'menu', 'passport', 'modularity', 'system'];

    /**
     * @var array 各应用默认的核心模块
     */
    protected $_appCoreModules = [
        'backend' => ['account', 'action', 'data', 'log', 'menu', 'modularity', 'notification', 'operate', 'system', 'passport'],
        'frontend' => [],
        'console' => [],
    ];

    /**
     * 各应用默认的模块命名空间，包括：`系统核心模块命名空间`和`开发者模块命名空间`。
     * 应用下包含的键值有：
     * - `core`: 系统核心模块命名空间，加载模块时系统会自动转换该命名空间为模块目录并搜索其下所有有效的模块
     * - `developer`: 开发者模块命名空间，加载模块时系统会自动转换该命名空间为模块目录并搜索其下所有有效的模块
     *
     * @var array
     * @see getAppModuleNamespace()
     * @see setAppModuleNamespace()
     */
    protected $_appModuleNamespace = [
        'backend' => [
            'core' => 'wocenter\backend\modules',
            'developer' => 'backend\modules',
        ],
        'frontend' => [
            'core' => 'wocenter\frontend\modules',
            'developer' => 'frontend\modules',
        ],
        'console' => [
            'core' => 'wocenter\console\modules',
            'developer' => 'console\modules',
        ],
    ];

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

        return $installedModule
            ? $this->getLoad()->filterModules($this->getLoad()->getAllModuleConfig(), $installedModule)
            : [];
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
                    /** @var Module $moduleModel */
                    $moduleModel = Yii::createObject($this->moduleModel);
                    $installedModule = $moduleModel::find()
                        ->select('id, run_module')
                        ->where(['app' => Yii::$app->id])
                        ->indexBy('id')->asArray()->all();
                    if (!$installedModule) {
                        return [];
                    }
                    $allModuleParts = $this->getLoad()->getAllModuleConfig(true);
                    foreach ($installedModule as $moduleId => $row) {
                        // 运行模块不是开发者模块则不加载开发者模块
                        if ($row['run_module'] != $moduleModel::RUN_MODULE_DEVELOPER) {
                            unset($allModuleParts['developer'][$moduleId]);
                        }
                    }
                    $allModuleParts['core'] = $this->getLoad()->filterModules($allModuleParts['core'], array_keys($installedModule));

                    return ArrayHelper::getColumn(
                        array_merge($allModuleParts['core'], $allModuleParts['developer']),
                        'moduleClass'
                    );
                }, $this->cacheDuration, null, 'commonCache');
                break;
            case 'uninstall':
                $arr = Wc::getOrSet([$appId, self::CACHE_UNINSTALL_MODULES], function () {
                    /** @var Module $moduleModel */
                    $moduleModel = Yii::createObject($this->moduleModel);
                    // 已经安装的模块ID数组
                    $installedModuleIds = $moduleModel->getInstalledModuleId();
                    // 系统存在的模块ID数组
                    $existModuleIds = array_keys($this->getLoad()->getAllModuleConfig());

                    // 未安装的模块ID数组
                    return array_diff($existModuleIds, $installedModuleIds);
                }, $this->cacheDuration, null, 'commonCache');
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
        $dbModules = $moduleModel::find()
            ->select('id,is_system,run_module,status')
            ->where(['app' => Yii::$app->id])
            ->indexBy('id')->asArray()->all();
        $allModuleParts = $this->getLoad()->getAllModuleConfig(true);
        $allModules = array_merge($allModuleParts['core'], $allModuleParts['developer']);
        foreach ($allModules as $moduleId => &$v) {
            // 添加模块主键
            $v['id'] = $moduleId;
            // 数据库里存在模块信息则标识模块已安装
            if (array_key_exists($v['id'], $dbModules)) {
                $existModule = $dbModules[$v['id']];
                // 是否为系统模块
                $v['infoInstance']->isSystem =
                    $v['infoInstance']->isSystem
                    || $existModule['is_system']
                    || in_array($v['id'], $this->getCoreModules());
                // 系统模块不可卸载
                $v['infoInstance']->canUninstall = !$v['infoInstance']->isSystem;
                $v['status'] = $existModule['status'];
                $v['run_module'] = $existModule['run_module'];
            } else {
                // 数据库不存在数据则可以进行安装
                $v['infoInstance']->canInstall = true;
                $v['status'] = 0; // 未安装则为禁用状态
                $v['run_module'] = -1; // 未安装则没有正在运行的模块
            }
            // 开发者模块
            $v['developer_module'] = isset($allModuleParts['developer'][$moduleId]);
            // 核心模块
            $v['core_module'] = isset($allModuleParts['core'][$moduleId]);
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
        $allModuleParts = $this->getLoad()->getAllModuleConfig(true);
        $allModules = array_merge($allModuleParts['core'], $allModuleParts['developer']);

        if ($allModules[$id] == null) {
            throw new NotFoundHttpException('模块不存在');
        }

        if ($onDataBase) {
            /** @var Module $module */
            $module = $this->moduleModel;
            if (($module = $module::find()->where(['id' => $id, 'app' => Yii::$app->id])->one()) == null) {
                throw new NotFoundHttpException('模块暂未安装');
            }
            $module->infoInstance = $allModules[$id]['infoInstance'];
            // 系统模块及必须安装的模块不可卸载
            $module->infoInstance->canUninstall = !$module->infoInstance->isSystem && !$module->is_system && !in_array($id, $this->getCoreModules());
        } else {
            /** @var Module $module */
            $module = new $this->moduleModel();
            $module->infoInstance = $allModules[$id]['infoInstance'];
            $module->infoInstance->canInstall = true;
            $module->id = $id;
            $module->app = Yii::$app->id;
            $module->is_system = $module->infoInstance->isSystem ? 1 : 0;
            $module->run_module = isset($allModuleParts['developer'][$id])
                ? $module::RUN_MODULE_DEVELOPER
                : $module::RUN_MODULE_CORE;
            $module->status = 1;
        }
        // 有效的运行模块列表
        $validRunModuleList = [];
        $runModuleList = $module->getRunModuleList();
        // 开发者模块
        if (isset($allModuleParts['developer'][$id])) {
            $validRunModuleList[$module::RUN_MODULE_DEVELOPER] = $runModuleList[$module::RUN_MODULE_DEVELOPER];
        }
        // 核心模块
        if (isset($allModuleParts['core'][$id])) {
            $validRunModuleList[$module::RUN_MODULE_CORE] = $runModuleList[$module::RUN_MODULE_CORE];
        }
        $module->setValidRunModuleList($validRunModuleList);

        return $module;
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
     * - 删除当前应用模块缓存
     * - 删除已安装模块缓存
     * - 删除已安装模块的路由规则缓存
     * - 删除未安装模块缓存
     */
    public function clearCache()
    {
        $appId = Yii::$app->id;
        Wc::cache()->delete([$appId, self::CACHE_INSTALLED_MODULES]);
        Wc::cache()->delete([$appId, self::CACHE_UNINSTALL_MODULES]);
        Wc::cache()->delete([$appId, self::CACHE_MODULE_URL_RULE]);
        $this->clearAllModuleConfig();
    }

    /**
     * 删除所有模块缓存信息
     */
    public function clearAllModuleConfig()
    {
        Wc::cache()->delete([
            Yii::$app->id,
            self::CACHE_ALL_MODULE_CONFIG,
            true,
        ]);
        Wc::cache()->delete([
            Yii::$app->id,
            self::CACHE_ALL_MODULE_CONFIG,
            false,
        ]);
    }

    /**
     * 获取各应用默认的模块命名空间
     *
     * @return array
     */
    public function getAppModuleNamespace()
    {
        return $this->_appModuleNamespace;
    }

    /**
     * 设置指定应用默认的模块命名空间
     * 以更改`backend`应用模块命名空间为例，可能的配置如下：
     * [
     *  'backend' => [
     *      'core' => 'wocenter/backend/module',
     *      'developer' => 'backend/module',
     *  ]
     * ]
     *
     * @param array $config 命名空间配置
     *
     * @return array
     */
    public function setAppModuleNamespace($config)
    {
        $this->_appModuleNamespace = array_merge($this->_appModuleNamespace, $config);
    }

    /**
     * 获取当前应用的系统核心模块命名空间
     *
     * @return string
     */
    public function getCoreModuleNamespace()
    {
        return $this->_appModuleNamespace[Yii::$app->id]['core'];
    }

    /**
     * 获取当前应用的开发者模块命名空间
     *
     * @return string
     */
    public function getDeveloperModuleNamespace()
    {
        return $this->_appModuleNamespace[Yii::$app->id]['developer'];
    }

    /**
     * 获取各应用默认的核心模块
     *
     * @return array
     */
    public function getAppCoreModules()
    {
        return $this->_appCoreModules;
    }

    /**
     * 设置各应用默认的核心模块
     * 以更改`frontend`应用核心模块为例，可能的配置如下：
     * [
     *  'frontend' => [
     *      'passport',
     *  ]
     * ]
     *
     * @param array $appCoreModules 核心模块配置
     */
    public function setAppCoreModules($appCoreModules)
    {
        $this->_appCoreModules = array_merge($this->_appCoreModules, $appCoreModules);
    }

    /**
     * 获取当前应用的核心模块
     *
     * @return array
     */
    public function getCoreModules()
    {
        return isset($this->_appCoreModules[Yii::$app->id]) ? $this->_appCoreModules[Yii::$app->id] : [];
    }

}
