<?php
namespace wocenter\traits;

use wocenter\core\Controller;
use wocenter\core\Dispatch;
use wocenter\Wc;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\helpers\Inflector;

/**
 * Class DispatchTrait
 * 主要为Controller控制器增加系统调度功能
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait DispatchTrait
{

    /**
     * @var string 调度器基础路径，默认为[[wocenter\services\DispatchService::getBasePath()]]设置的路径
     * @see wocenter\services\DispatchService::getBasePath()
     */
    public $dispatchBasePath;

    /**
     * @var string 调用指定主题的调度器，默认为[[wocenter\services\DispatchService::$theme]]设置的[[$theme]]主题
     * @see wocenter\services\DispatchService::$theme
     */
    public $dispatchTheme;

    /**
     * 系统调度器配置
     *
     * @return array
     */
    public function dispatches()
    {
        return [];
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
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     */
    public function getDispatch($route = null)
    {
        $dispatchService = Wc::$service->getDispatch();
        $createService = $dispatchService->getCreate();
        // 是否调用指定主题的调度器
        if ($this->dispatchTheme !== null) {
            $dispatchService->theme = $this->dispatchTheme;
        }
        // 是否指定自定义调度器基础路径，系统会调用该路径下指定路由的调度器
        if ($this->dispatchBasePath !== null) {
            $dispatchService->getView()->basePath = $this->dispatchBasePath;
        }
        // 没有指定调度路由则默认获取主题公共调度器
        if ($route === null) {
            $className = $dispatchService->getCommonNamespace();

            return $createService->create('common', $className, $this);
        } else {
            return $this->getDispatchByRoute($route);
        }
    }

    protected function getDispatchByRoute($route)
    {
        $dispatchService = Wc::$service->getDispatch();
        $createService = $dispatchService->getCreate();
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
            $controller = $this;
            $actionId = Inflector::camelize($route);
            $route = $createService->getUniqueId() . '/' . $actionId; // {$moduleId}/{$controllerId}/View
        } // 路由地址为：comment/view
        elseif ($pos > 0) {
            $controllerId = substr($route, 0, $pos);
            $controller = $this->module->createControllerByID($controllerId);
            if ($controller === null) {
                throw new InvalidRouteException('Unable to resolve the dispatch request: ' . $route);
            }
            $oldController = Yii::$app->controller;
            Yii::$app->controller = $controller;
            $controllerId = $createService->normalizeName($controllerId);
            $actionId = Inflector::camelize(substr($route, $pos + 1));
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
                $actionId = Inflector::camelize($actionID);
                $controllerId = $createService->normalizeName($controller->id);
                $moduleId = $controller->module->id;
                $route = implode('/', [$moduleId, $controllerId, $actionId]); // admin/comment/View
            } else {
                throw new InvalidRouteException('Unable to resolve the dispatch request: ' . $requestRoute);
            }
        }

        $className = $dispatchService->getNamespace($route);
        $dispatch = $createService->create($actionId, $className, $controller);
        if ($dispatch === null) {
            throw new InvalidRouteException('Unable to resolve the dispatch request: ' . $route);
        }

        if ($oldController !== null) {
            Yii::$app->controller = $oldController;
        }

        Yii::trace('Loading dispatch: ' . $route, __METHOD__);

        return $dispatch;
    }

    /**
     * @inheritdoc
     */
    public function createAction($id)
    {
        $action = parent::createAction($id);

        return $action === null ? Wc::$service->getDispatch()->getCreate()->createByConfig($id) : $action;
    }

    /**
     * 操作失败后返回结果至客户端
     *
     * @param string|array $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     */
    public function error($message = '', $jumpUrl = '', $data = [])
    {
        $this->getDispatch()->error($message, $jumpUrl, $data);
    }

    /**
     * 操作成功后返回结果至客户端
     *
     * @param string|array $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为1妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     */
    public function success($message = '', $jumpUrl = '', $data = [])
    {
        $this->getDispatch()->success($message, $jumpUrl, $data);
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
        return $this->getDispatch()->display($view ?: $this->action->id, $assign);
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
     * 是否全页面加载
     *
     * @param \yii\web\Request $request
     *
     * @return boolean
     */
    public function isFullPageLoad($request = null)
    {
        return $this->getDispatch()->isFullPageLoad($request);
    }

}
