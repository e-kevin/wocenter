<?php
namespace wocenter\services;

use wocenter\core\Dispatch;
use wocenter\core\Service;
use wocenter\core\View;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Controller;

/**
 * 调度服务类
 *
 * @property View $view Dispatch需要使用的view组件，默认使用Yii::$app->getView()组件
 * @author E-Kevin <e-kevin@qq.com>
 */
class DispatchService extends Service
{

    /**
     * @var View Dispatch需要使用的view组件，默认使用Yii::$app->getView()组件
     */
    private $_view;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'dispatch';
    }

    /**
     * 根据路由获取指定的Dispatch调度，主要用于获取调度类除[[run()]]方法外的其他方法
     *
     * @param string|null $route 路由信息，用于指定获取哪个调度层数据，为null时自动获取控制器当前路由，默认为null
     * @param null $theme 主题名，用于指定获取哪个主题的调度数据
     *
     * @return Dispatch
     * @throws InvalidConfigException
     */
    public function get($route = null, $theme = null)
    {
        $result = $this->handleRequest($route);
        $normalizeRoute = $result[0];
        if (!Yii::$app->has($normalizeRoute)) {
            $dispatchNamespace = $this->getDispatchNamespace($normalizeRoute, $theme);
            Yii::$app->set($normalizeRoute, [
                'class' => $dispatchNamespace,
                'controller' => $this->createController($normalizeRoute),
                'view' => $result[1],
            ]);

            Yii::trace('Create dispatch: ' . $dispatchNamespace, __METHOD__);
        }

        return Yii::$app->get($normalizeRoute);
    }

    /**
     * 执行Dispatch调度，该方法是[[$this->get()->run()]]的快捷方法
     *
     * @param null $route 路由信息，用于指定获取哪个调度层数据，为null时自动获取控制器当前路由，默认为null
     * @param null $theme 主题名，用于指定获取哪个主题的调度数据
     *
     * @return mixed
     */
    public function run($route = null, $theme = null)
    {
        $dispatch = $this->get($route, $theme);

        Yii::trace('Running dispatch: ' . get_class($dispatch) . '::run()', __METHOD__);

        return $dispatch->run();
    }

    /**
     * 根据路由生成所需控制器
     *
     * 1) 系统不存在激活的控制器，则自动创建一个公共控制器
     *      一般这种情况的发生是在控制器初始化阶段[[Controller::init()]]时需要调用
     *      调度器里的[[\wocenter\core\Dispatch::error()]]方法进行页面信息提示和跳转时触发，
     *      因为此阶段并没有生成激活的控制器
     * @see \wocenter\backend\core\Controller::init()
     *
     * 2) 如果路由为当前模块下的路由，则直接返回当前控制器，否则根据路由创建对应的控制器
     *
     * @param $route
     *
     * @see \yii\base\Module::createController()
     *
     * @return Controller
     */
    protected function createController($route)
    {
        if (Yii::$app->controller == null) {
            return Yii::createObject('yii\web\Controller', ['common', null]);
        }
        $parts = explode('/', $route);
        $controller = Yii::$app->controller;
        // 跨模块调用则新建控制器
        if ($parts[1] !== $controller->module->id) {
            $controller = $controller->module->createController($route)[0];
        }

        return $controller;
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
     * 获取主题目录路径，默认获取指定主题`$theme`内调度层的基础目录
     * 如果需要修改此路径，只需配置\wocenter\core\View::$basePath属性即可，因为系统默认dispatch数据存放于主题目录内
     *
     * @see \wocenter\core\View::$basePath
     *
     * @param string $theme 主题名
     * @param string $path 主题内路径
     *
     * @return string
     */
    protected function getBasePath($theme, $path = 'dispatch')
    {
        // 如果没有指定调用哪个主题的调度层，则调用view组件的默认主题调度层
        if ($theme == null) {
            $theme = $this->getView()->themeName;
        }

        $basePath = $this->getView()->getBasePath();

        return implode(DIRECTORY_SEPARATOR, [$basePath, $theme, $path]);
    }

    /**
     * 获取调度器命名空间
     *
     * @param $route
     * @param $theme
     *
     * @return mixed
     */
    protected function getDispatchNamespace($route, $theme)
    {
        $classFile = $this->getBasePath($theme, $route) . '.php';
        if (is_file(Yii::getAlias($classFile))) {
            return str_replace(DIRECTORY_SEPARATOR, '\\', substr($classFile, 1, -4));
        } else {
            return str_replace(DIRECTORY_SEPARATOR, '\\', substr($this->getBasePath($theme, 'components/Dispatch'), 1));
        }
    }

    /**
     * 处理调度请求
     *
     * 1) 系统不存在激活的控制器，则默认获取主题目录下的公共调度器
     * 2) 系统存在激活控制器，则根据当前控制器自动补全路由信息
     *
     * @param null|string $route 为null时自动获取控制器当前路由，否则根据以下规则返回完整路由信息
     *
     * @return array [$route, $action]
     */
    private function handleRequest($route = null)
    {
        // 1) 系统不存在激活的控制器，则默认获取主题目录下的公共调度器
        if (Yii::$app->controller == null) {
            $route = 'components/Dispatch';
            $action = null;

            Yii::trace('Dispatch requested: \'' . $route . '\'', __METHOD__);
        } else {
            // 2) 系统存在激活控制器，则根据当前控制器自动补全路由信息
            $controller = Yii::$app->controller;
            if ($route == null) {
                $route = $controller->route;
            }
            $route = explode('/', ltrim($route, '/'));
            if (count($route) == 1) {
                // e.g. index
                array_unshift($route, $controller->module->id, $controller->id);
            } elseif (count($route) == 2) {
                // e.g. site/index
                array_unshift($route, $controller->module->id);
            } else {
                // e.g. backend/site/index 原样返回
            }
            $action = $route[2];
            // 格式化调度类名
            $route[1] = $this->normalizeName($route[1]);
            $route[2] = Inflector::camelize($route[2]);
            $route = implode('/', $route);

            Yii::trace('Dispatch requested: \'' . $route . '\'', __METHOD__);
            $route = 'dispatch/' . $route; // 存在激活控制器则获取调度层里的调度器
        }

        return [$route, $action];
    }

    /**
     * 格式化带'-'的字符窜。例如：控制器带'-'，如`config-manager`，调度器在处理请求路由时，因命名空间不支持带'-'的命名方式，因此需要
     * 处理该字符窜，操作将返回如`configManager`这样格式的字符窜
     *
     * @param string $string
     *
     * @return string
     */
    protected function normalizeName($string)
    {
        if (($pos = strpos($string, '-')) !== false) {
            $string = Inflector::variablize($string);
        }

        return $string;
    }

    /**
     * 提示用户创建调度器文件
     */
    public function generateRunFile()
    {
        $controller = Yii::$app->controller;
        $route = $controller->route;
        $route = explode('/', ltrim($route, '/'));
        if (count($route) == 1) {
            // e.g. index
            array_unshift($route, $controller->module->id, $controller->id);
        } elseif (count($route) == 2) {
            // e.g. site/index
            array_unshift($route, $controller->module->id);
        } else {
            // e.g. backend/site/index 原样返回
        }
        // 格式化调度类名
        $name = Inflector::camelize(ArrayHelper::remove($route, 2));

        $classFile = $this->getBasePath(null, implode('/', $route));

        return '请在 ' . $classFile . ' 目录下创建调度文件 ' . $name . '.php';
    }

}
