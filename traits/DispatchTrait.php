<?php

namespace wocenter\traits;

use wocenter\core\Dispatch;
use wocenter\helpers\StringHelper;
use wocenter\interfaces\ExtensionInterface;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class DispatchTrait
 * 主要为Controller控制器增加系统调度功能
 *
 * @method null|Dispatch getCommonDispatch($route = null) 控制器必须继承\wocenter\interfaces\CommonDispatchInterface()接口类
 * @method void initDispatchEnvironment() 初始化调度器运行环境
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait DispatchTrait
{
    
    /**
     * @var int 运行模式，可选值有：
     *  - 0: 运行系统扩展
     * @see ExtensionInterface::RUN_MODULE_EXTENSION
     *  - 1: 运行开发者扩展
     * @see ExtensionInterface::RUN_MODULE_DEVELOPER
     *
     * 如果该值被指定，则获取调度器时该值优先级最高
     */
    public $runMode;
    
    /**
     * @var string 调度器命名空间，默认该值在初始化时由系统自动生成
     * @see Controller::init()
     */
    public $dispatchNamespace;
    
    /**
     * @var string 开发者调度器命名空间，默认该值在初始化时由系统自动生成
     * @see Controller::init()
     */
    public $developerDispatchNamespace;
    
    /**
     * @var string the ID of this controller.
     */
    public $id;
    
    /**
     * @var Module the module that this controller belongs to.
     */
    public $module;
    
    /**
     * @var string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'index'.
     */
    public $defaultAction = 'index';
    
    /**
     * @var array 当前调度器所属扩展的数据库配置信息
     */
    private $_extensionConfig;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->initDispatchEnvironment();
    }
    
    /**
     * @inheritdoc
     */
    public function createAction($id)
    {
        return parent::createAction($id) ?: $this->_createDispatch($id);
    }
    
    /**
     * 操作失败后返回结果至客户端
     *
     * @param string|array $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * @return mixed
     */
    public function error($message = '', $jumpUrl = '', $data = [])
    {
        return $this->getDispatch()->error($message, $jumpUrl, $data);
    }
    
    /**
     * 操作成功后返回结果至客户端
     *
     * @param string|array $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为1妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * @return mixed
     */
    public function success($message = '', $jumpUrl = '', $data = [])
    {
        return $this->getDispatch()->success($message, $jumpUrl, $data);
    }
    
    /**
     * 显示页面
     *
     * @param string $view
     * @param array $assign
     *
     * @return string|\yii\web\Response
     */
    public function display($view = null, $assign = [])
    {
        return $this->getDispatch()->display($view, $assign);
    }
    
    /**
     * 保存视图模板文件赋值数据
     *
     * 示例：
     * ```php
     *  $this->assign('name1', 'apple');
     *  $this->assign('name2', 'orange');
     *  等于
     *  $this->assign([
     *      'name1' => 'apple',
     *      'name2' => 'orange'
     *  ]);
     *
     * ```
     *
     * 使用该方法可向当前控制器的调度器内传入公共模板数据
     *
     * @param string|array $key
     * @param string|array $value
     *
     * @return Dispatch
     */
    public function assign($key = '', $value = '')
    {
        return $this->getDispatch()->assign($key, $value);
    }
    
    /**
     * 调度器配置
     *
     * 调度配置为数组，支持以下键名配置
     *  - `class`: 使用该类创建调度器，该类必须继承`wocenter\core\Dispatch`。注意：当该值被指定，以下配置将不生效
     *  - `dispatchOptions`: 调度器配置，可以使用的配置键如下：
     *   - `map`: 使用其他调度器映射。如：
     *   ```php
     *      'update' => [
     *          'dispatchOptions' => [
     *              'map' => 'edit', // 使用调度器配置映射，将调用'Edit'调度器替代原来的'Update'调度器
     *           ]
     *      ]
     *   ```
     *  或者：
     *   ```php
     *      'update' => 'edit', // 直接使用调度器映射
     *   ```
     *  注意：配置映射后，如果调度器内使用的是[[display()]]方法进行页面渲染而没有指定方法内的`$view`参数，则该方法将
     *  自动用所调用的调度器ID所对应的视图文件进行渲染（如：[[Update]]用[[Edit]]进行映射后所对应的是`update`
     *  视图文件而不是映射后的`edit`），如果需要用[[Edit]]调度器所对应的视图文件（如：`edit`）进行渲染，
     *  则只需要显式配置`$view`参数即可，如：[[display('edit')]]
     *
     * @return array
     */
    public function dispatches()
    {
        return [];
    }
    
    /**
     * 格式化带'-_'字符的调度器路由地址
     * 例如：ConfigManager控制器，路由地址为'config-manager'，调度器在处理路由地址时，因命名空间不支持带'-'的命名方式，
     * 因此需要处理该字符窜，操作将返回如`configManager`这样格式的字符窜
     *
     * @param string $route
     *
     * @return string
     */
    public function normalizeDispatchNamespace($route)
    {
        $route = explode('/', $route);
        foreach ($route as &$part) {
            if (strpos($part, '-') !== false) {
                $part = Inflector::variablize($part);
            }
        }
        
        return ($this->_isDeveloperMode() ? $this->developerDispatchNamespace : $this->dispatchNamespace)
            . '\\' . str_replace('/', '\\', implode('/', $route));
    }
    
    /**
     * 格式化调度器类名
     * 例如：操作路由为'invite-signup'，调度器在处理路由地址时，因命名空间不支持带'-'的命名方式，
     * 因此需要处理该字符窜，操作将返回如`InviteSignup`这样格式的字符窜
     *
     * @param string $string
     *
     * @return string
     */
    public function normalizeDispatchName($string)
    {
        return Inflector::camelize($string);
    }
    
    /**
     * 根据路由地址获取调度器，默认获取主题公共调度器
     *
     * 该方法和[[run()|runAction()]]方法类似，唯一区别是在获取到指定调度器时不默认执行[[run()]]，而是可以自由调用调度器里面的方法，
     * 这样可以有效实现部分代码重用
     *
     * @param null|string $route 调度路由，支持以下格式：'view', 'comment/view', '/admin/comment/view'
     *
     * @return null|Dispatch
     */
    public function getDispatch($route = null)
    {
        // 没有指定调度路由则默认获取主题公共调度器
        return $route === null ? $this->getCommonDispatch() : $this->_getDispatchByRoute($route);
    }
    
    /**
     * 根据调度器配置创建调度器
     *
     * @param string $id 调度器ID
     * @param string|array $config 调度器类名或调度器配置信息
     *
     * @return null|Dispatch|object
     * @throws Exception
     * @throws InvalidConfigException
     */
    private function _createDispatchByConfig($id, $config)
    {
        if (!is_array($config)) {
            $config = ['class' => $config];
        }
        
        internal:
        $className = ltrim($config['class'], '\\');
        
        $dispatch = null;
        if (class_exists($className)) {
            if (is_subclass_of($className, 'wocenter\core\Dispatch')) {
                $dispatch = Yii::createObject($config, [
                    $id,
                    Yii::$app->controller ?: $this,
                ]);
                
                if (get_class($dispatch) !== $className) {
                    $dispatch = null;
                }
            } elseif (YII_DEBUG) {
                throw new InvalidConfigException("Dispatch class must extend from \\wocenter\\core\\Dispatch.");
            }
        } // 调度器不存在则调用系统扩展内调度器
        elseif ($this->_isDeveloperMode()) {
            $config['class'] = StringHelper::replace($config['class'], 'developer\\');
            if (class_exists($config['class'])) {
                goto internal;
            }
        }
        
        if ($dispatch === null) {
            $this->_generateDispatchFile($className);
        }
        
        Yii::trace('Loading dispatch: ' . $className, __METHOD__);
        
        return $dispatch;
    }
    
    /**
     * 根据调度器ID创建调度器
     *
     * @param string $id 调度器ID
     * @param bool $withConfig 是否使用调度器配置。默认使用，即根据`[[dispatches()]]`配置来创建调度器
     *
     * @return null|Dispatch
     * @throws InvalidConfigException
     */
    private function _createDispatch($id, $withConfig = true)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }
        
        if ($withConfig) {
            $dispatchMap = $this->dispatches();
            // 存在调度配置信息则执行自定义调度，否则终止调度行为
            if (in_array($id, $dispatchMap) || isset($dispatchMap[$id])) {
                // 存在调度配置信息
                if (isset($dispatchMap[$id]) && !empty($dispatchMap[$id])) {
                    $config = $dispatchMap[$id];
                    // 调度配置为数组
                    if (is_array($config)) {
                        $dispatchOptions = ArrayHelper::remove($config, 'dispatchOptions', []);
                        // 调度配置包含`class`键名则直接使用`class`创建调度器
                        if (isset($config['class'])) {
                            $classConfig = $config;
                        } else {
                            $dispatchId = $this->id . '/' . $this->normalizeDispatchName($dispatchOptions['map'] ?? $id);
                            $classConfig = array_merge(
                                ['class' => $this->normalizeDispatchNamespace($dispatchId)],
                                $config // 使用其他属性初始化调度器
                            );
                        }
                    } // 调度配置为类名
                    elseif (class_exists($config)) {
                        $classConfig = $config;
                    } // 其他字符串则为直接调度器映射
                    else {
                        $dispatchId = $this->id . '/' . $this->normalizeDispatchName($config);
                        $classConfig = $this->normalizeDispatchNamespace($dispatchId);
                    }
                } // 根据调度器ID直接创建调度器
                else {
                    $dispatchId = $this->id . '/' . $this->normalizeDispatchName($id);
                    $classConfig = $this->normalizeDispatchNamespace($dispatchId);
                }
            } else {
                return null;
            }
        } else {
            $dispatchId = $this->id . '/' . $this->normalizeDispatchName($id);
            $classConfig = $this->normalizeDispatchNamespace($dispatchId);
        }
        
        return $this->_createDispatchByConfig($id, $classConfig);
    }
    
    /**
     * 根据路由地址获取调度器
     *
     * @param string $route 调度路由，支持以下格式：'view', 'comment/view', '/admin/comment/view'
     *
     * @return null|Dispatch
     * @throws InvalidRouteException
     */
    private function _getDispatchByRoute($route)
    {
        $pos = strpos($route, '/');
        if ($pos === false) {
            $parts = [$this, $route];
        } elseif ($pos > 0) {
            $parts = $this->module->createController($route);
        } else {
            $parts = Yii::$app->createController($route);
        }
        if (is_array($parts)) {
            /* @var $controller self */
            list($controller, $actionID) = $parts;
            $oldController = Yii::$app->controller;
            Yii::$app->controller = $controller;
        } else {
            throw new InvalidRouteException('Unable to resolve the dispatch request: ' . $route);
        }
        
        $dispatch = $controller->_createDispatch($actionID, false);
        
        if ($oldController !== null) {
            Yii::$app->controller = $oldController;
        }
        
        return $dispatch;
    }
    
    /**
     * 调度器不存在则抛出友好提示信息
     *
     * @param string $className 调度器类名
     *
     * @throws Exception
     */
    private function _generateDispatchFile($className)
    {
        $file = '@' . str_replace('\\', '/', ltrim($className, '\\')) . '.php';
        $file = str_replace('\\', DIRECTORY_SEPARATOR, Yii::getAlias($file));
        throw new Exception("请在该路径下创建调度器文件:\r\n{$file}");
    }
    
    /**
     * @return bool 是否开发者运行模式
     */
    private function _isDeveloperMode()
    {
        return $this->runMode == ExtensionInterface::RUN_MODULE_DEVELOPER
            || (
                $this->runMode !== ExtensionInterface::RUN_MODULE_EXTENSION
                && $this->_extensionConfig['run'] == ExtensionInterface::RUN_MODULE_DEVELOPER
            );
    }
    
}
