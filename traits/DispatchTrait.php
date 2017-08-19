<?php
namespace wocenter\traits;

use wocenter\core\Dispatch;
use wocenter\Wc;
use Yii;

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
     * 该方法与[[actions()]]唯一的区别是可以根据[[$dispatchBasePath]]、[[$dispatchTheme]]、系统主题配置等配置数据自动
     * 获取相应的调度器执行结果返回给客户端
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
     */
    public function getDispatch($route = null)
    {
        return Wc::$service->getDispatch()->getDispatch($route, $this);
    }

    /**
     * @inheritdoc
     */
    public function createAction($id)
    {
        $action = parent::createAction($id);

        return $action ?: Wc::$service->getDispatch()->getCreate()->createByConfig($id);
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
     *
     * @return mixed
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
