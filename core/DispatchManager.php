<?php

namespace wocenter\core;

use wocenter\helpers\StringHelper;
use wocenter\interfaces\DispatchManagerInterface;
use wocenter\interfaces\ExtensionInfoInterface;
use wocenter\interfaces\RunningExtensionInterface;
use wocenter\traits\DispatchTrait;
use wocenter\Wc;
use Yii;
use yii\base\BaseObject;
use yii\base\Controller;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

/**
 * 系统调度功能（Dispatch）管理类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DispatchManager extends BaseObject implements DispatchManagerInterface
{
    
    /**
     * 调用该管理器的控制器
     *
     * @var Controller|DispatchTrait
     */
    protected $controller;
    
    /**
     * 运行模式，可选值有：
     *  - 0: 运行系统扩展，即运行'@extensions'目录下的扩展
     * @see ExtensionInfoInterface::RUN_MODULE_EXTENSION
     *  - 1: 运行开发者扩展，即运行'@developer'目录下的扩展
     * @see ExtensionInfoInterface::RUN_MODULE_DEVELOPER
     *
     * 可以通过设置该值为'1'，然后在开发者目录里添加需要替换的调度器，即可简单实现对系统扩展内调度器的替换，
     * 此方法无需修改系统扩展内的调度器，从而避免扩展升级后可能带来不兼容或文件被覆盖等问题。
     *
     * 注意：
     *  - 如果该值被指定，则获取调度器时该值优先级最高。
     *  - 当控制器为开发者控制器或用户自定义控制器时，系统将自动视为开发者运行模式。
     *  - 如果调度器配置明确指明了'class'类，则该配置失效。
     *
     * @var int
     */
    public $runMode;
    
    /**
     * 默认调度器配置，即控制器默认提供的调度器
     *
     * 调度配置为数组，支持以下键值配置或键名-键值对配置
     * 键值配置：
     *  ```php
     *      ['index', 'view', 'update']
     *  ```
     * 键名-键值对配置：
     *  - `class`: 使用该类创建调度器，该类必须继承`wocenter\core\Dispatch`。
     *  - `viewPath`: 使用该视图文件来进行渲染。
     *  - `map`: 使用其他调度器进行映射，目前仅支持同控制器下的调度器映射。
     *      注意：当{class}被明确指定后，该配置将不生效。
     *      如：
     *      ```php
     *          'update' => [
     *              'class' => '{namespace}/Update',
     *              'map' => 'edit', // 使用调度器配置映射，将调用'Edit'调度器替代原来的'Update'调度器
     *              'viewPath' => 'edit', // 用'edit'视图替代原来的视图
     *          ]
     *      ```
     *      'viewPath': 该值同时可以使用视图文件同步标记来实现视图文件跟随调度器位置进行同步定位，设置方法为：
     * 在视图文件名ID前加'#'定位符即可。
     * 如调度器位于'@app/dispatches/{themeName}'，当前控制器位于'@extensions/system/controllers'，
     * 默认的系统会在'@extensions/system/themes/{themeName}'目录下寻找视图文件，如果加上'#'定位符，则系统
     * 会在'@app/themes/{themeName}'目录下寻找视图文件。
     *      ```php
     *          'update' => [
     *              'class' => '@app/dispatches/{themeName}/Update',
     *              'viewPath' => '#edit', // 根据定位符，自动在'@app/themes/{themeName}'目录下寻找视图文件
     *          ]
     *      ```
     *      或者：
     *      ```php
     *          'update' => 'edit', // 直接使用调度器映射
     *      ```
     *      注意：配置映射后，如果调度器内使用的是[[display($view)]]方法进行页面渲染而没有指定方法内的`$view`参数，
     * 则该方法将自动用所调用的调度器ID所对应的视图文件进行渲染（如：[[Update]]用[[Edit]]进行映射后所对应的是update`
     * 视图文件而不是映射后的`edit`），如果需要用[[Edit]]调度器所对应的视图文件（如：`edit`）进行渲染，则只需要显式
     * 配置`$view`参数即可，如：[[display('edit')]]
     * @see \wocenter\core\web\Dispatch::display()
     *
     * @var array
     */
    private $_defaultDispatches = [];
    
    /**
     * 获取不到调度器时，将在调度器目录内的{$defaultDispatch}目录下查找。
     * 当该值为非字符串时，表示禁用该功能。
     *
     * @var string|bool 默认调度器主题目录名
     */
    public $defaultDispatch = 'basic';
    
    /**
     * @var string|array 默认调度器行为类
     */
    public $defaultDispatchBehavior;
    
    /**
     * @param Controller $controller 调用该管理器的控制器类
     * @param array $defaultDispatches 默认的调度器配置
     * @param array $config
     */
    public function __construct(Controller $controller, array $defaultDispatches, array $config = [])
    {
        $this->controller = $controller;
        $this->_defaultDispatches = $this->_normalizeDispatchConfig($defaultDispatches);
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        // 当前控制器存在全局调度配置数据
        if (isset($this->config[$this->controller->getUniqueId()])) {
            $config = $this->config[$this->controller->getUniqueId()];
            // 覆盖当前控制器的运行模式，确保配置优先级更高
            if (isset($config['runMode'])) {
                $this->controller->runMode = $config['runMode'];
            }
            // 赋值当前控制器的全局调度配置数据
            if (isset($config['dispatchMap'])) {
                $this->_preDispatchMap = $config['dispatchMap'];
            }
        }
        // 合并当前控制器的调度配置数据，全局配置数据优先级最高
        if (!empty($this->controller->dispatchMap)) {
            $this->_preDispatchMap = ArrayHelper::merge(
                $this->_normalizeDispatchConfig($this->controller->dispatchMap),
                $this->_preDispatchMap
            );
        }
    }
    
    /**
     * @var array 当前控制器未被规范的调度器配置，该值在[[init()]]初始化阶段时由系统设置
     * @see init()
     */
    private $_preDispatchMap = [];
    
    /**
     * @var array 当前控制器的调度器配置
     */
    private $_dispatchMap;
    
    /**
     * @inheritdoc
     */
    public function getDispatchMap(): array
    {
        if (null === $this->_dispatchMap) {
            if (empty($this->_preDispatchMap)) {
                return $this->_dispatchMap = $this->_defaultDispatches;
            }
            $this->_dispatchMap = ArrayHelper::merge($this->_defaultDispatches, $this->_preDispatchMap);
            foreach ($this->_dispatchMap as $key => $value) {
                if (strpos($key, '@') !== false) {
                    foreach ($value as $k => $v) {
                        // `class`被明确指定时，`map`映射配置将不生效
                        if (isset($v['class'])) {
                            unset($this->_dispatchMap[$key][$k]['map']);
                        }
                    }
                } else {
                    // `class`被明确指定时，`map`映射配置将不生效
                    if (isset($value['class'])) {
                        unset($this->_dispatchMap[$key]['map']);
                    }
                }
            }
        }
        
        return $this->_dispatchMap;
    }
    
    /**
     * @var array 全局调度器配置
     */
    private $_config = [];
    
    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return $this->_config;
    }
    
    /**
     * 设置全局调度器配置，该方法可让用户通过配置方式动态调节控制器内的调度器配置数据，支持新增或替换等操作方法
     *
     * @see $_defaultDispatches 配置规则请参考该属性
     *
     * @param array $config 调度器配置数据
     *
     * @example
     * 应用场景：
     * @extensions目录存在名为'yii2-controller-site'的系统扩展，目录结构如下：
     *  extensions
     *      - yii2-controller-site
     *          - controllers // 存放控制器
     *              - SiteController
     *          - dispatches // 存放调度器
     *              - Index
     * 用户可通过路由地址'site/index'进行访问，'site'对应的控制器名为'SiteController'，
     * 现在为'SiteController'控制器添加一个用于测试的调度器，调度器位于'@app/dispatches'目录下，名为'Test'。
     * 现在可通过以下方式进行添加，而无需在'yii2-controller-site'目录里添加或更改任何文件，
     * 从而避免升级等操作可能导致更改被覆盖的问题。
     * [
     *      // 用路由地址为键名，该键名为控制器实际对应的路由名称
     *      'site' => [
     *          // 通过调度映射配置进行添加
     *          'dispatchMap' => [
     *              // '调度器名可以为任何规范的路由地址名称，此处用'test'名
     *              'test' => [
     *                  'class' => 'app\dispatches\Test', // Test调度器用该类实现
     *                  'property1' => 'value1',
     *                  'property2' => 'value2',
     *                  ......, // 可以用其他属性值对该类进行配置
     *              ],
     *          ],
     *      ],
     * ]
     * 配置完成后，即可通过路由地址'site/test'进行访问。
     * 通常，对于系统扩展的更改，WoCenter建议把更改文件存放于'@developer'开发者目录内对应的目录里。遵循该守则，
     * 即可通过配置'runMode'属性值简单进行调度器映射，而无需每次配置都指定相应的调度器类。
     * 使用该方法，'@developer'开发者目录内的结构大致如下：
     *  developer
     *      - yii2-controller-site
     *          - dispatches // 存放调度器
     *              - Test
     * [
     *      'site' => [
     *          // 只需要设置'runMode'值为'1'，即可启用开发者运行模式。该模式下，调度管理器会优先获取开发者目录内的调度器
     *          'runMode' => 1,
     *          'dispatchMap' => [
     *              'test', // 此处只需设置路由地址名即可，系统会自动根据'test'名获取'Test'调度器
     *          ],
     *      ],
     * ]
     * 调度器配置同样适用于模块扩展的设置：
     * [
     *      // 支持模块路由地址
     *      'extension/module' => [
     *          'runMode' => 1,
     *          'dispatchMap' => [
     *          ],
     *      ],
     * ]
     *
     * @return array
     */
    public function setConfig(array $config): array
    {
        foreach ($config as $key => $value) {
            // 过滤不规范的配置数据
            if (is_int($key) || !is_array($value)) {
                continue;
            }
            if (isset($value['dispatchMap'])) {
                $this->_config[$key]['dispatchMap'] = $this->_normalizeDispatchConfig($value['dispatchMap']);
            }
            if (isset($value['runMode'])) {
                $this->_config[$key]['runMode'] = $value['runMode'];
            }
        }
        
        return $this->_config;
    }
    
    /**
     * 标准化调度器配置数据
     *
     * @param array $config 调度器配置数据
     *
     * @return array
     */
    private function _normalizeDispatchConfig(array $config): array
    {
        $arr = [];
        foreach ($config as $key => $value) {
            // ['test', 'adminlte/test']
            if (is_int($key)) {
                if (strpos($value, '/') !== false) {
                    list($themeName, $key) = explode('/', $value);
                    $arr['@' . $themeName][$key] = [];
                } else {
                    $arr[$value] = [];
                }
            } // ['test' => [], 'adminlte/test' => [], 'basic/test' => 'test1']
            elseif (is_string($key)) {
                if (strpos($key, '/') !== false) {
                    list($themeName, $key) = explode('/', $key);
                    if (is_array($value)) {
                        $arr['@' . $themeName][$key] = $value;
                    } elseif (strpos($value, '\\') !== false) {
                        $arr['@' . $themeName][$key]['class'] = $value;
                    } elseif (is_string($value)) {
                        $arr['@' . $themeName][$key]['map'] = $value;
                    }
                } else {
                    if (is_array($value)) {
                        $arr[$key] = $value;
                    } elseif (strpos($value, '\\') !== false) {
                        $arr[$key]['class'] = $value;
                    } elseif (is_string($value)) {
                        $arr[$key]['map'] = $value;
                    }
                }
            } // 暂不支持禁用调度器
            else {
            }
        }
        
        return $arr;
    }
    
    /**
     * @inheritdoc
     */
    public function getDispatch($route = null)
    {
        if (null === $route) {
            return $this->_createDispatchByConfig($this->controller->action->id, [
                'class' => 'wocenter\core\web\Dispatch',
                'as dispatch' => $this->getThemeConfig()['dispatch'], // 主题公共调度器行为类
            ]);
        }
        $pos = strpos($route, '/');
        if ($pos === false) {
            return $this->createDispatch($route);
        } elseif ($pos > 0) {
            $parts = $this->controller->module->createController($route);
        } else {
            $parts = Yii::$app->createController($route);
        }
        if (is_array($parts)) {
            /* @var Controller|DispatchTrait $controller */
            list($controller, $actionID) = $parts;
            $oldController = Yii::$app->controller;
            Yii::$app->controller = $controller;
        } else {
            throw new InvalidRouteException('Unable to resolve the request: ' . $route);
        }
        
        $dispatch = $controller->getDispatchManager()->createDispatch($actionID);
        
        if ($oldController !== null) {
            Yii::$app->controller = $oldController;
        }
        
        return $dispatch;
    }
    
    /**
     * @inheritdoc
     */
    public function createDispatch($id)
    {
        if ($id === '') {
            $id = $this->controller->defaultAction;
        }
        
        // 不存在调度配置信息则终止调度行为
        if (null === ($config = $this->getCurrentDispatchMap($id))) {
            return null;
        }
        
        /**
         * 使用调度器映射后，替换原调度器ID，但保持原有默认的渲染视图文件名为原调度器ID的文件名，
         * 该方法主要适用于不同调度器同时调用同一个公共调度器，但渲染视图文件时，仍使用映射前的视图文件ID。
         * 'viewPath'可用值可参考[[\yii\base\View::render()]]方法的[[$view]]设置。
         * @see \yii\base\View::render()
         */
        $config['viewConfig']['viewPath'] = $config['viewConfig']['viewPath'] ?? $id; // 替换或保持原有调度器ID的视图文件名
        $config['viewConfig']['themeName'] = $config['viewConfig']['themeName'] ?? $this->getThemeConfig()['name'];
        // 默认调度器名
        $defaultDispatch = ArrayHelper::remove($config, 'defaultDispatch', null);
        // 开发者运行模式
        $runMode = ArrayHelper::remove($config, 'runMode', null);
        
        // 没有指定'class'时，'$runMode'、'$map'、'$defaultDispatch'配置生效，并自动由调度管理器获取所需调度器
        if (!isset($config['class'])) {
            $id = ArrayHelper::remove($config, 'map', $id); // 替换为映射后的调度器ID
            $route = $this->controller->id . '/' . $id;
            // 调度器类名
            $class = '%s' . $this->getThemeConfig()['name'] . '\\' . $this->_normalizeDispatchRoute($route);
            $extDispatch = $this->getRunningExtension()->getNamespace() . '\\dispatches\\';
            /**
             * 获取默认调度器
             *
             * @param string $class
             *
             * @return string
             */
            $getDefaultDispatch = function ($class) use ($defaultDispatch) {
                if (!is_string($defaultDispatch) && is_string($this->defaultDispatch)) {
                    $defaultDispatch = $this->defaultDispatch;
                }
                if (is_string($defaultDispatch) &&
                    (strpos($class, 'dispatches\\' . $defaultDispatch . '\\') === false)
                ) {
                    $oldClass = $class;
                    $class = preg_replace(
                        '/(dispatches\\\)(\w)+/',
                        'dispatches\\' . $defaultDispatch,
                        $class);
                    if (!class_exists($class)) {
                        return $oldClass;
                    }
                    Yii::info(
                        $oldClass . ': Dispatch does not exist and automatically calls default dispatch.',
                        __METHOD__
                    );
                }
                
                return $class;
            };
            // 开发者运行模式下
            if ($this->_enableDeveloperMode($runMode)) {
                $devDispatch = StringHelper::replace($extDispatch, 'extensions', 'developer');
                $config['class'] = sprintf($class, $devDispatch);
                // 如果开发者调度器不存在，则获取开发者下的默认调度器
                if (!class_exists($config['class'])) {
                    $config['class'] = $getDefaultDispatch($config['class']);
                    // 开发者默认调度器不存在，则调用系统扩展内调度器
                    if (!class_exists($config['class'])) {
                        goto defaultDispatch;
                    }
                }
            } else {
                defaultDispatch:
                $config['class'] = sprintf($class, $extDispatch);
                if (!class_exists($config['class'])) {
                    $config['class'] = $getDefaultDispatch($config['class']);
                } elseif ($this->_enableDeveloperMode($runMode)) {
                    Yii::info(
                        'Developer extension dispatch does not exist and automatically calls system extension dispatch.',
                        __METHOD__
                    );
                }
            }
        }
        
        return $this->_createDispatchByConfig($id, $config);
    }
    
    /**
     * 根据调度器配置创建调度器
     *
     * @param string $id 调度器ID
     * @param array $config 调度器配置信息
     *
     * @return null|Dispatch|object
     * @throws Exception
     * @throws InvalidConfigException
     */
    private function _createDispatchByConfig(string $id, array $config)
    {
        $config['class'] = ltrim($config['class'], '\\');
        $dispatch = null;
        if (class_exists($config['class'])) {
            if (is_subclass_of($config['class'], 'wocenter\core\Dispatch')) {
                $viewConfig = ArrayHelper::remove($config, 'viewConfig', [
                    'viewPath' => $id,
                    'themeName' => $this->getThemeConfig()['name'],
                ]);
                // 初始化调度器行为类
                if (!isset($config['as dispatch'])) {
                    $config['as dispatch'] = $this->defaultDispatchBehavior ?? $this->getThemeConfig()['dispatch'];
                }
                $dispatch = Yii::createObject($config, [$id, $this->controller,]);
                if (get_class($dispatch) !== $config['class']) {
                    $dispatch = null;
                }
                $this->_setDispatchViewPath($config['class'], $viewConfig);
            } elseif (YII_DEBUG) {
                throw new InvalidConfigException("Dispatch class must extend from \\wocenter\\core\\Dispatch.");
            }
        }
        
        if (null === $dispatch) {
            $this->_generateDispatchFile($config['class']);
        } else {
            Yii::debug('Loading dispatch: ' . $config['class'], __METHOD__);
        }
        
        return $dispatch;
    }
    
    /**
     * 获取当前调度器配置信息
     *
     * @param string $id 当前调度器ID
     *
     * @return array|null
     * @example
     * ```php
     * [
     *      'runMode' => 1,
     *      'defaultDispatch' => 'basic',
     *      'viewConfig' => [
     *          'viewPath => '#index',
     *          'themeName' => 'basic',
     *      ],
     * ]
     * ```
     */
    protected function getCurrentDispatchMap($id)
    {
        // 通用调度器配置存在指定类名，则优先获取
        if (isset($this->dispatchMap[$id]['class'])) {
            $config = $this->dispatchMap[$id];
        } // 获取指定主题的调度器配置
        elseif (isset($this->dispatchMap['@' . $this->getThemeConfig()['name']][$id])) {
            $config = $this->dispatchMap['@' . $this->getThemeConfig()['name']][$id];
        } // 获取通用调度器配置
        elseif (isset($this->dispatchMap[$id])) {
            $config = $this->dispatchMap[$id];
        } // 不存在调度配置信息则终止调度行为
        else {
            $config = null;
        }
        
        return $config;
    }
    
    /**
     * 调度器不存在则抛出友好提示信息
     *
     * @param string $className 调度器类名
     *
     * @throws NotFoundHttpException
     */
    private function _generateDispatchFile($className)
    {
        if (YII_DEBUG) {
            $file = '@' . str_replace('\\', '/', $className) . '.php';
            throw new NotFoundHttpException("请在该路径下创建调度器文件:\r\n" . Yii::getAlias($file));
        }
    }
    
    /**
     * 格式化带'-_'字符的路由地址为命名空间所支持的格式
     * 例如：
     * ```php
     * [
     *      'config-manager/view' => 'ConfigManager\View'
     * ]
     * ```
     *
     * @param string $route 调度路由
     *
     * @return string
     */
    private function _normalizeDispatchRoute(string $route): string
    {
        $route = explode('/', $route);
        foreach ($route as &$part) {
            $part = Inflector::camelize($part);
        }
        
        return implode('\\', $route);
    }
    
    /**
     * 是否启用开发者运行模式，只有当前控制器属于系统扩展控制器才生效，
     * 当控制器为开发者控制器或用户自定义控制器时，将禁用开发者运行模式。
     *
     * @param null $runMode 当前调度器配置的运行模式
     *
     * @return bool
     */
    private function _enableDeveloperMode($runMode = null): bool
    {
        if ($this->getRunningExtension()->isExtensionController()) {
            // 全局禁用
            if ($this->runMode === ExtensionInfoInterface::RUN_MODULE_EXTENSION) {
                return false;
            } elseif ($runMode === ExtensionInfoInterface::RUN_MODULE_EXTENSION) {
                return false;
            } elseif ($runMode == ExtensionInfoInterface::RUN_MODULE_DEVELOPER) {
                return true;
            } elseif ($this->controller->runMode === ExtensionInfoInterface::RUN_MODULE_EXTENSION) {
                return false;
            } elseif (
                $this->controller->runMode === ExtensionInfoInterface::RUN_MODULE_DEVELOPER ||
                $this->getRunningExtension()->getDbConfig()['run'] == ExtensionInfoInterface::RUN_MODULE_DEVELOPER ||
                $this->runMode === ExtensionInfoInterface::RUN_MODULE_DEVELOPER
            ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 设置调度器视图路径
     *
     * @param string $className 调度器类名
     * @param array $viewConfig 视图配置
     */
    private function _setDispatchViewPath($className, $viewConfig)
    {
        // 是否跟随调度器自动同步需要渲染的视图目录，默认不同步。
        $syncView = false;
        // 视图文件是否存在同步标记
        if (strrpos($viewConfig['viewPath'], '#') === 0) {
            $this->_viewPath = substr($viewConfig['viewPath'], 1);
            $syncView = true;
        } else {
            $this->_viewPath = $viewConfig['viewPath'];
        }
        $viewPath = '%s' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $viewConfig['themeName'] .
            DIRECTORY_SEPARATOR . $this->controller->id;
        $path = '';
        // 跟随调度器所在位置的上级目录设定视图路径
        if ($syncView) {
            if (($pos = strrpos($className, '\\dispatches')) !== false) {
                $path = StringHelper::ns2Path(substr($className, 0, $pos));
            }
        } // 以当前控制器所在位置的上级目录设定视图路径
        else {
            $path = StringHelper::ns2Path($this->getRunningExtension()->getNamespace());
        }
        
        $this->controller->setViewPath(sprintf($viewPath, $path));
    }
    
    /**
     * @var string 调度器需要渲染的视图文件路径，可用值可参考[[\yii\base\View::render()]]方法的[[$view]]设置。
     * @see \yii\base\View::render()
     * 该默认值在[[_setDispatchViewPath()]]方法里设置
     * @see _setDispatchViewPath()
     */
    private $_viewPath;
    
    /**
     * @inheritdoc
     */
    public function getViewPath(): string
    {
        return $this->_viewPath;
    }
    
    private $_themeConfig;
    
    /**
     * @inheritdoc
     */
    public function getThemeConfig(): array
    {
        if (null === $this->_themeConfig) {
            $this->_themeConfig = Wc::getThemeConfig($this->controller);
        }
        
        return $this->_themeConfig;
    }
    
    /**
     * @var RunningExtensionInterface 当前控制器所属的扩展信息
     */
    private $_runningExtension;
    
    /**
     * @inheritdoc
     */
    public function getRunningExtension()
    {
        if (null === $this->_runningExtension) {
            $this->_runningExtension = Wc::getRunningExtension($this->controller);
        }
        
        return $this->_runningExtension;
    }
    
}
