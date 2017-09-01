<?php
namespace wocenter\services\modularity;

use wocenter\core\Service;
use wocenter\helpers\ArrayHelper;
use wocenter\helpers\FileHelper;
use wocenter\interfaces\ModularityInfoInterface;
use wocenter\services\ModularityService;
use wocenter\Wc;
use Yii;
use yii\base\Exception;

/**
 * 加载模块配置服务类
 *
 * @property array $moduleConfig 搜索模块目录，默认获取所有模块信息
 * @property mixed $urlRules 获取模块路由规则
 * @property array $menus 获取模块菜单配置数据
 * @property array $migrationPath 获取模块数据库迁移目录
 * @property array $bootstraps 获取需要执行bootstrap的模块
 * @property array $modulePathConfig 获取模块路径配置信息
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class LoadService extends Service
{

    /**
     * @var ModularityService 父级服务类
     */
    public $service;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'load';
    }

    /**
     * 获取模块路由规则
     *
     * @return mixed
     */
    public function getUrlRules()
    {
        return Wc::getOrSet([
            Yii::$app->id,
            ModularityService::CACHE_MODULE_URL_RULE,
        ], function () {
            $arr = [];
            // 获取所有已经安装的模块配置文件
            foreach ($this->service->getInstalledModules() as $moduleId => $row) {
                /* @var $infoInstance ModularityInfoInterface */
                $infoInstance = $row['infoInstance'];
                $arr = ArrayHelper::merge($arr, $infoInstance->getUrlRules());
            }

            return $arr;
        }, $this->service->cacheDuration);
    }

    /**
     * 获取模块菜单配置数据
     *
     * @return array
     */
    public function getMenus()
    {
        $arr = [];
        // 获取所有已经安装的模块配置文件
        foreach ($this->service->getInstalledModules() as $moduleId => $row) {
            /* @var $infoInstance ModularityInfoInterface */
            $infoInstance = $row['infoInstance'];
            $arr = ArrayHelper::merge($arr, $this->_formatMenuConfig($infoInstance->getMenus()));
        }

        return $arr;
    }

    /**
     * 获取模块数据库迁移目录
     *
     * @return array
     */
    public function getMigrationPath()
    {
        return ArrayHelper::getColumn($this->getModuleConfig(), 'migrationPath');
    }

    /**
     * 获取需要执行bootstrap的模块
     *
     * @return array
     */
    public function getBootstraps()
    {
        $bootstrap = [];
        // 获取所有已经安装的模块配置文件
        foreach ($this->service->getInstalledModules() as $moduleId => $row) {
            /** @var \wocenter\core\ModularityInfo $instance */
            $instance = $row['infoInstance'];
            if ($instance->bootstrap) {
                $bootstrap[] = $moduleId;
            }
        }

        return $bootstrap;
    }

    /**
     * 获取模块路径配置信息，包括系统核心模块和开发者模块
     *
     * @return array
     */
    public function getModulePathConfig()
    {
        return [
            'core' => [
                'path' => $this->service->getCoreModulePath(),
                'namespace' => $this->service->coreModuleNamespace,
            ],
            'developer' => [
                'path' => $this->service->getDeveloperModulePath(),
                'namespace' => $this->service->developerModuleNamespace,
            ],
        ];
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
     * 搜索模块目录，默认获取所有模块信息
     *
     * @param array $modules 只获取该数组模块信息，如：['account', 'passport', ...]
     *
     * @return array [
     * [
     *  moduleId => [
     *      moduleClass,
     *      infoInstance,
     *      migrationPath
     *  ]
     * ]
     */
    public function getModuleConfig($modules = [])
    {
        $allModuleFiles = Wc::getOrSet([
            Yii::$app->id,
            ModularityService::CACHE_ALL_MODULE_FILES,
        ], function () {
            $allModules = [];
            foreach ($this->getModulePathConfig() as $config) {
                $modulePath = $config['path'];
                if (($moduleRootDir = @dir($modulePath))) {
                    while (($moduleFolder = $moduleRootDir->read()) !== false) {
                        $currentModuleDir = $modulePath . DIRECTORY_SEPARATOR . $moduleFolder;
                        if (preg_match('|^\.+$|', $moduleFolder) || !FileHelper::isDir($currentModuleDir)) {
                            continue;
                        }

                        $namespacePrefix = $config['namespace'] . '\\' . $moduleFolder;

                        // 搜索 WoCenter 核心模块类
                        if (FileHelper::exist($currentModuleDir . DIRECTORY_SEPARATOR . 'Module.php')) {
                            $moduleClass = $namespacePrefix . '\Module';
                        } else {
                            continue;
                        }

                        // 初始化模块类，获取相关信息
                        try {
                            /** @var \yii\base\Module $module */
                            $module = Yii::createObject($moduleClass, [$moduleFolder, Yii::$app]);
                        } catch (\Exception $e) {
                            throw new Exception($e->getMessage());
                        }

                        // 初始化模块信息类
                        $infoClass = $namespacePrefix . '\Info';
                        try {
                            /** @var \wocenter\core\ModularityInfo $instance */
                            $instance = Yii::createObject($infoClass, [$module->id, [
                                'version' => $module->version,
                            ]]);
                            $instance->name = $instance->name ?: $moduleFolder;
                        } catch (\Exception $e) {
                            // 不存在模块信息类则意味着该模块不接受系统模块管理
                            continue;
                        }

                        // 模块数据库迁移目录
                        $migrationPath = '@' . str_replace('\\', '/', $namespacePrefix . '/migrations');

                        $allModules[$instance->id] = [
                            'moduleClass' => $moduleClass,
                            'infoInstance' => $instance,
                            'migrationPath' => $migrationPath,
                        ];
                    }
                }
            }

            return $allModules;
        }, $this->service->cacheDuration);

        // 只获取`$modules`数组模块信息
        if ($this->service->debug || !empty($modules)) {
            foreach ($allModuleFiles as $moduleId => $row) {
                if (
                    // 开启调试模式，则只获取指定模块
                    ($this->service->debug && !in_array($moduleId, $this->service->debugModules)) ||
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

}
