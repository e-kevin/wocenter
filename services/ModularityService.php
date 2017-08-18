<?php
namespace wocenter\services;

use wocenter\core\Service;
use wocenter\helpers\FileHelper;
use wocenter\models\Module;
use wocenter\Wc;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 管理系统模块类
 *
 * @property string $moduleRootPath 模块目录
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
    const CACHE_UNINSTALLED_MODULES = 'uninstalledModules';

    /**
     * 缓存所有模块信息
     */
    const CACHE_ALL_MODULE_FILES = 'allModuleFiles';

    /**
     * @var boolean 开启调试模式
     */
    public $debug = false;

    /**
     * @var array 调试模块。主要用于读取所需测试的模块目录，其他目录不加载
     */
    public $debugModules = ['account', 'menu', 'passport', 'modularity', 'log'];

    /**
     * @var array 核心模块，必须安装
     */
    public $coreModules = ['modularity', 'menu', 'system', 'data'];

    /**
     * @var string|array|callable|Module 模块类
     */
    public $moduleModel = '\wocenter\models\Module';

    /**
     * @var string 模块命名空间
     */
    public $moduleNamespace = 'wocenter\backend\modules';

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

        return $this->loadModuleFiles($moduleModel->getInstalledModuleId());
    }

    /**
     * 获取当前应用未安装的模块ID
     *
     * @param integer|boolean $duration 缓存时间
     *
     * @return array 未安装的模块ID
     */
    public function getUninstalledModuleId($duration = 60)
    {
        return $this->getModulesInternal('uninstall', $duration);
    }

    /**
     * 获取已安装的模块配置信息，用于设置系统模块Yii::$app->setModule()
     *
     * @param integer|boolean $duration 缓存时间
     *
     * @return array [
     * [
     *  $moduleId => 'moduleNamespace',
     * ]
     */
    public function getInstalledModuleConfigs($duration = 60)
    {
        return $this->getModulesInternal('install', $duration);
    }

    /**
     * 根据$type类型获取应用相关模块
     *
     * @param string $type 模块类型
     * @param integer|boolean $duration 缓存时间
     *
     * @return array
     */
    protected function getModulesInternal($type, $duration = 60)
    {
        switch ($type) {
            case 'install':
                $arr = Wc::getOrSet(self::CACHE_INSTALLED_MODULES, function () {
                    return ArrayHelper::getColumn(
                        $this->getInstalledModules(),
                        'moduleNamespace'
                    );
                }, $duration);
                break;
            case 'uninstall':
                $arr = Wc::getOrSet(self::CACHE_UNINSTALLED_MODULES, function () {
                    /** @var Module $moduleModel */
                    $moduleModel = Yii::createObject($this->moduleModel);
                    // 已经安装的模块ID数组
                    $installedModuleIds = $moduleModel->getInstalledModuleId();
                    // 系统存在的模块ID数组
                    $existModuleIds = ArrayHelper::getColumn($this->loadModuleFiles(), 'id');

                    // 未安装的模块ID数组
                    return array_diff($existModuleIds, $installedModuleIds);
                }, $duration);
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
        $allModules = $this->loadModuleFiles();
        foreach ($allModules as &$v) {
            // 数据库里存在模块信息则标识模块已安装
            if (array_key_exists($v['id'], $dbModules)) {
                $existModule = $dbModules[$v['id']];
                // 系统模块及必须安装的模块不可卸载
                $v['infoInstance']->canUninstall =
                    !$v['infoInstance']->isSystem &&
                    !$existModule['is_system'] &&
                    !in_array($v['id'], $this->coreModules);
                // 是否为系统模块
                $v['infoInstance']->isSystem = $v['infoInstance']->isSystem || $existModule['is_system'];
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
        $modules = $this->loadModuleFiles();
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
     * 根据模块命名空间自动获取模块目录
     *
     * @return boolean|string
     */
    public function getModulePath()
    {
        return Yii::getAlias('@' . str_replace('\\', '/', $this->moduleNamespace));
    }

    /**
     * 搜索模块目录，默认获取所有模块信息
     *
     * @param array $modules 只获取该数组模块信息，如：['account', 'passport', ...]
     *
     * @return array [
     * [
     *  moduleId => [
     *      id,
     *      moduleNamespace,
     *      infoInstance
     *  ]
     * ]
     */
    public function loadModuleFiles($modules = [])
    {
        $allModuleFiles = Wc::getOrSet(self::CACHE_ALL_MODULE_FILES, function () {
            $allModules = [];
            $modulePath = $this->getModulePath();
            if (($moduleRootDir = @dir($modulePath))) {
                while (($moduleFolder = $moduleRootDir->read()) !== false) {
                    $currentModuleDir = $modulePath . DIRECTORY_SEPARATOR . $moduleFolder;
                    if (preg_match('|^\.+$|', $moduleFolder) || !FileHelper::isDir($currentModuleDir)) {
                        continue;
                    }

                    // 搜索模块信息数据
                    if (FileHelper::exist($currentModuleDir . DIRECTORY_SEPARATOR . 'Info.php')) {
                        $class = $this->moduleNamespace . '\\' . $moduleFolder . '\\' . 'Info';
                    } else {
                        continue;
                    }

                    // 初始化模块信息类
                    /** @var \wocenter\core\ModularityInfo $instance */
                    try {
                        $instance = new $class();
                        $instance->id = $instance->id ?: $moduleFolder;
                        $instance->name = $instance->name ?: $moduleFolder;
                    } catch (\Exception $e) {
                        $instance = null;
                    }

                    if ($instance !== null) {
                        $allModules[$instance->id] = [
                            'id' => $instance->id,
                            'moduleNamespace' => $this->moduleNamespace . '\\' . $instance->id . "\\Module",
                            'infoInstance' => $instance,
                        ];
                    }
                }
            }

            return $allModules;
        }, 60);

        if ($this->debug || !empty($modules)) {
            foreach ($allModuleFiles as $moduleId => $row) {
                if (
                    // 开启调试模式，则只获取指定模块
                    ($this->debug && !in_array($moduleId, $this->debugModules)) ||
                    // 只获取该数组模块信息
                    (!empty($modules) && !in_array($moduleId, $modules))
                ) {
                    unset($allModuleFiles[$moduleId]);
                    continue;
                }
            }
        }

        return $allModuleFiles;
    }

    /**
     * 删除缓存
     * - 删除所有模块缓存
     */
    public function clearCache()
    {
        Yii::$app->getCache()->delete(self::CACHE_INSTALLED_MODULES);
        Yii::$app->getCache()->delete(self::CACHE_UNINSTALLED_MODULES);
        Yii::$app->getCache()->delete(self::CACHE_ALL_MODULE_FILES);
    }

}
