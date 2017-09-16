<?php
namespace wocenter\services;

use wocenter\core\Controller;
use wocenter\core\Dispatch;
use wocenter\core\Service;
use wocenter\core\View;
use wocenter\Wc;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\helpers\Inflector;

/**
 * 调度服务类
 *
 * @property View $view Dispatch需要使用的view组件，默认使用Yii::$app->getView()组件
 * @property string $commonNamespace 获取主题公共调度器，默认调度器为主题目录下的components文件夹的Dispatch文件，只读属性
 * @property \wocenter\services\dispatch\CreateService $create 创建调度器服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DispatchService extends Service
{

    /**
     * @var string 调用指定主题的调度器，默认为Yii::$app->getView()组件设置的[[$themeName]]主题
     * @see wocenter\core\View::$themeName
     */
    public $theme;

    /**
     * @var View Dispatch需要使用的view组件，默认使用Yii::$app->getView()组件
     * @see wocenter\core\View
     */
    protected $_view;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'dispatch';
    }
    
    /**
     * 获取Dispatch需要使用的view组件
     *
     * @return View
     */
    public function getView()
    {
        if ($this->_view == null) {
            $this->setView(Yii::$app->getView());
        }

        return $this->_view;
    }

    /**
     * 设置Dispatch需要使用的view组件
     *
     * @param View $view
     *
     * @throws InvalidConfigException
     */
    public function setView($view)
    {
        if (!$view instanceof View) {
            throw new InvalidConfigException('The Dispatch Service needs to be used by the view component to inherit `\wocenter\core\View`');
        }
        $this->_view = $view;
    }

    /**
     * 格式化带'-_'字符的控制器名
     * 例如：ConfigManager控制器，路由地址为'config-manager'，调度器在处理路由地址时，因命名空间不支持带'-'的命名方式，
     * 因此需要处理该字符窜，操作将返回如`configManager`这样格式的字符窜
     *
     * @param string $string
     *
     * @return string
     */
    public function normalizeControllerName($string)
    {
        if (($pos = strpos($string, '-')) !== false) {
            $string = Inflector::variablize($string);
        }

        return $string;
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
     * 格式化调度器视图文件名
     * 例如：调度器名为'InviteSignup'，处理后返回值为'invite-signup'
     *
     * @param string $string
     *
     * @return string
     */
    public function normalizeDispatchViewFileName($string)
    {
        return Inflector::camel2id($string);
    }

    /**
     * 获取指定路由地址的调度器命名空间
     *
     * 执行顺序如下：
     * 1. 开发者调度器目录无法获取到相应路由的调度器，则执行下一步，否则返回获取到的调度器命名空间
     * 2. 系统核心调度器目录存在相应路由的调度器命名空间则直接返回，否则返回开发者调度器命名空间
     *
     * @param string $route 路由地址
     * @param string $path 主题目录下的路径，默认为主题下的调度器目录
     *
     * @return string 调度器命名空间
     */
    public function getNamespace($route, $path = 'dispatches')
    {
        // 是否调用指定主题的调度器
        if ($this->theme !== null) {
            $this->getView()->themeName = $this->theme;
        }

        // 添加对当前[运行模块]的支持，不是运行模块的调度器不加载
        if ($this->getIsRunningCoreModule()) {
            $coreDispatchPath = $this->getView()->getCoreThemePath($path);
            $coreNamespace = $this->_getNamespaceByRoute($route, $coreDispatchPath);

            return $coreNamespace;
        } else {
            // 开发者调度器目录
            $developerDispatchPath = $this->getView()->getDeveloperThemePath($path);
            $developerNamespace = $this->_getNamespaceByRoute($route, $developerDispatchPath);
            if (!class_exists($developerNamespace)) {
                // 系统核心调度器目录
                $coreDispatchPath = $this->getView()->getCoreThemePath($path);
                $coreNamespace = $this->_getNamespaceByRoute($route, $coreDispatchPath);
                if (class_exists($coreNamespace)) {
                    return $coreNamespace;
                }
            }

            return $developerNamespace;
        }
    }

    /**
     * @return boolean 是否运行核心模块
     */
    public function getIsRunningCoreModule()
    {
        return Yii::$app->controller !== null && strncmp(Yii::$app->controller->module->className(), 'wocenter', 8) == 0;
    }

    /**
     * @param string $route 路由地址
     * @param string $dispatchPath 调度器路径
     *
     * @return mixed
     */
    protected function _getNamespaceByRoute($route, $dispatchPath)
    {
        return str_replace('/', '\\', substr($dispatchPath . '/' . $route, 1));
    }

    /**
     * 获取主题公共调度器，默认调度器为主题目录下的components文件夹的Dispatch文件
     *
     * @return string 调度器命名空间
     */
    public function getCommonNamespace()
    {
        return $this->getNamespace('Dispatch', 'components');
    }

    /**
     * 根据路由地址获取调度器，默认获取主题公共调度器
     *
     * 该方法和[[run()|runAction()]]方法类似，唯一区别是在获取到指定调度器时不默认执行[[run()]]，而是可以自由调用调度器里面的方法，
     * 这样可以有效实现部分代码重用
     *
     * @param null|string $route 调度路由，支持以下格式：'view', 'comment/view', '/admin/comment/view'
     * @param Controller $controller 调用调度器的控制器
     *
     * @return null|Dispatch
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws \yii\base\Exception
     */
    public function getDispatch($route = null, $controller)
    {
        $createService = $this->getCreate();
        // 是否调用指定主题的调度器
        if ($controller->dispatchTheme !== null) {
            $this->getView()->themeName = $controller->dispatchTheme;
        }

        // 没有指定调度路由则默认获取主题公共调度器
        if ($route === null) {
            $className = $this->getCommonNamespace();
            if (($dispatch = $createService->create('common', $className, $controller)) === null) {
                $createService->generateDispatchFile($className);
            }

            Yii::trace('Loading dispatch: ' . $className, __METHOD__);

            return $dispatch;
        } else {
            return $this->_getDispatchByRoute($route, $controller);
        }
    }

    /**
     * 根据路由地址获取调度器
     *
     * @param string $route 调度路由，支持以下格式：'view', 'comment/view', '/admin/comment/view'
     * @param Controller $controller 调用调度器的控制器
     *
     * @return null|Dispatch
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     */
    protected function _getDispatchByRoute($route, Controller $controller)
    {
        $createService = $this->getCreate();
        /**
         * 当前参数：
         * ```php
         * $moduleId = 'admin';
         * $controllerId = 'comment';
         * $actionId = 'view';
         * ```
         */
        $pos = strpos($route, '/');
        $oldController = null;
        // 路由地址为：view
        if ($pos === false) {
            $actionId = $this->normalizeDispatchName($route);
            $route = $createService->getUniqueId() . '/' . $actionId; // {$moduleId}/{$controllerId}/View
        } // 路由地址为：comment/view
        elseif ($pos > 0) {
            $controllerId = substr($route, 0, $pos);
            $controller = $controller->module->createControllerByID($controllerId);
            if ($controller === null) {
                throw new InvalidRouteException('Unable to resolve the dispatch request: ' . $route);
            }
            $oldController = Yii::$app->controller;
            Yii::$app->controller = $controller;
            $controllerId = $this->normalizeControllerName($controllerId);
            $actionId = $this->normalizeDispatchName(substr($route, $pos + 1));
            $route = $controller->module->id . '/' . $controllerId . '/' . $actionId; // {$moduleId}/comment/View
        } // 路由地址为：/admin/comment/view
        else {
            $route = trim($route, '/');
            $requestRoute = $route;
            $route = explode('/', $route);
            // 确保格式必须包括{$moduleId}/{$controllerId}/{$actionId}
            if (count($route) < 3) {
                throw new InvalidRouteException('Unable to resolve the dispatch request: /' . $requestRoute);
            }
            $parts = Yii::$app->createController($requestRoute);
            if (is_array($parts)) {
                /* @var $controller Controller */
                list($controller, $actionID) = $parts;
                $oldController = Yii::$app->controller;
                Yii::$app->controller = $controller;
                $actionId = $this->normalizeDispatchName($actionID);
                $controllerId = $this->normalizeControllerName($controller->id);
                $moduleId = $controller->module->id;
                $route = implode('/', [$moduleId, $controllerId, $actionId]); // admin/comment/View
            } else {
                throw new InvalidRouteException('Unable to resolve the dispatch request: ' . $requestRoute);
            }
        }

        $className = $this->getNamespace($route);
        if (($dispatch = $createService->create($actionId, $className, $controller)) === null) {
            $createService->generateDispatchFile($className);
        }

        if ($oldController !== null) {
            Yii::$app->controller = $oldController;
        }

        Yii::trace('Loading dispatch: ' . $className, __METHOD__);

        return $dispatch;
    }

    /**
     * 创建调度器服务类
     *
     * @return \wocenter\services\dispatch\CreateService
     */
    public function getCreate()
    {
        return $this->getSubService('create');
    }

}
