<?php

namespace wocenter\services\extension;

use wocenter\core\FunctionInfo;
use wocenter\core\ModularityInfo;
use wocenter\core\Service;
use wocenter\core\ThemeInfo;
use wocenter\helpers\FileHelper;
use wocenter\helpers\StringHelper;
use wocenter\services\ExtensionService;
use wocenter\Wc;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * 加载扩展子服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class LoadService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * @var string 缓存所有扩展别名
     */
    const CACHE_ALL_EXTENSION_ALIASES = 'all_extension_aliases';
    
    /**
     * @var string 获取所有扩展配置信息以及扩展详情类
     */
    const CACHE_ALL_CONFIG = 'all_extension_config';
    
    /**
     * @var string 缓存所有扩展文件配置信息
     */
    const CACHE_ALL_FILE_CONFIG = 'all_extension_file_config';
    
    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'load';
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        Wc::cache()->delete(self::CACHE_ALL_EXTENSION_ALIASES);
        Wc::cache()->delete(self::CACHE_ALL_CONFIG);
        Wc::cache()->delete(self::CACHE_ALL_FILE_CONFIG);
    }
    
    /**
     * 获取所有扩展配置信息以及扩展详情类，带数据缓存功能
     *
     * @return array
     */
    public function getAllExtensionConfig()
    {
        return Wc::getOrSet(self::CACHE_ALL_CONFIG, function () {
            return $this->_getExtensionConfig();
        }, $this->cacheDuration, null, 'commonCache');
    }
    
    /**
     * 搜索本地目录，获取所有扩展配置信息以及扩展详情类
     *
     * @return array
     * @throws InvalidConfigException
     */
    protected function _getExtensionConfig()
    {
        $config = [];
        $configFiles = $this->_getConfigFiles();
        foreach ($configFiles as $name => $row) {
            // 扩展详情类
            $namespacePrefix = $row['autoload']['psr-4'][0];
            $realPath = $row['autoload']['psr-4'][1];
            $infoClass = $namespacePrefix . 'Info';
            if (!class_exists($infoClass)) {
                continue;
            } // 功能扩展
            elseif (is_subclass_of($infoClass, FunctionInfo::className())) {
                $files = FileHelper::findFiles($realPath . DIRECTORY_SEPARATOR . 'controllers', [
                    'only' => ['*Controller.php'],
                ]);
                if (empty($files)) {
                    continue;
                }
                $controllerFile = $files[0];
                $controllerName = substr($controllerFile, strrpos($controllerFile, DIRECTORY_SEPARATOR) + 1, -4);
                $class = $namespacePrefix . 'controllers\\' . $controllerName;
                $controllerId = Inflector::camel2id(substr($controllerName, 0, -10));
                if (!class_exists($class)) {
                    continue;
                }
                // 初始化扩展详情类
                /** @var FunctionInfo $infoInstance */
                $infoInstance = Yii::createObject([
                    'class' => $infoClass,
                    'id' => $controllerId,
                    'version' => $row['version'],
                    'migrationPath' => $realPath . DIRECTORY_SEPARATOR . 'migrations',
                ], [
                    $row['id'],
                ]);
                $infoInstance->name = $infoInstance->name ?:
                    ($infoInstance->moduleId ? "/{$infoInstance->moduleId}/{$infoInstance->id}" : $infoInstance->id);
                
                $config[$infoInstance->app]['controllers'][$name] = [
                    'class' => $class,
                    'infoInstance' => $infoInstance,
                ];
            } // 模块扩展
            elseif (is_subclass_of($infoClass, ModularityInfo::className())) {
                $class = $namespacePrefix . 'Module';
                if (!class_exists($class)) {
                    continue;
                }
                // 初始化扩展详情类
                /** @var ModularityInfo $infoInstance */
                $infoInstance = Yii::createObject([
                    'class' => $infoClass,
                    'version' => $row['version'],
                    'migrationPath' => $realPath . DIRECTORY_SEPARATOR . 'migrations',
                ], [
                    $row['id'],
                ]);
                $infoInstance->name = $infoInstance->name ?: $infoInstance->id;
                
                $config[$infoInstance->app]['modules'][$name] = [
                    'class' => $class,
                    'infoInstance' => $infoInstance,
                ];
            } // 主题扩展
            elseif (is_subclass_of($infoClass, ThemeInfo::className())) {
                // 初始化扩展详情类
                /** @var ThemeInfo $infoInstance */
                $infoInstance = Yii::createObject([
                    'class' => $infoClass,
                    'version' => $row['version'],
                    'viewPath' => $realPath . DIRECTORY_SEPARATOR . 'views',
                ], [
                    $row['id'],
                ]);
                
                $config[$infoInstance->app]['themes'][$name] = [
                    'infoInstance' => $infoInstance,
                ];
            } else {
                continue;
            }
        }
        
        return $config;
    }
    
    /**
     * 搜索本地目录，获取所有扩展文件配置信息
     *
     * @return array
     */
    protected function _getConfigFiles()
    {
        return Wc::getOrSet(self::CACHE_ALL_FILE_CONFIG, function () {
            $files = FileHelper::findFiles(StringHelper::ns2Path('extensions'), [
                'only' => ['config.php'],
            ]);
            if (empty($files)) {
                return [];
            }
            $config = [];
            foreach ($files as $file) {
                $file = require "{$file}";
                $name = ArrayHelper::remove($file, 'name');
                $psr4 = ArrayHelper::remove($file['autoload'], 'psr-4');
                $config[$name] = $file;
                if (!isset($config[$name]['version'])) {
                    $config[$name]['version'] = 'dev';
                }
                $config[$name]['autoload']['psr-4'] = [
                    array_keys($psr4)[0],
                    array_shift($psr4),
                ];
            }
            
            return $config;
        }, $this->cacheDuration, null, 'commonCache');
    }
    
    /**
     * 加载扩展别名
     * todo 只加载已安装的模块
     */
    public function loadAliases()
    {
        $aliases = Wc::getOrSet(self::CACHE_ALL_EXTENSION_ALIASES, function () {
            $aliases = [];
            $configFiles = $this->_getConfigFiles();
            foreach ($configFiles as $name => $row) {
                $namespacePrefix = '@' . str_replace('\\', '/', rtrim($row['autoload']['psr-4'][0], '\\'));
                $aliases[$namespacePrefix] = $row['autoload']['psr-4'][1];
            }
            
            return $aliases;
        }, $this->cacheDuration, null, 'commonCache');
        foreach ($aliases as $namespacePrefix => $realPath) {
            Yii::setAlias($namespacePrefix, $realPath);
        }
    }
    
}
