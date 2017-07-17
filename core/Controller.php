<?php
namespace wocenter\core;

use yii\web\Controller as baseController;
use wocenter\Wc;

/**
 * 基础Controller类
 *
 * @property Dispatch $dispatch 获取调度
 * @author E-Kevin <e-kevin@qq.com>
 */
class Controller extends baseController
{

    /**
     * 获取调度
     *
     * @param null $route 路由信息，用于指定获取哪个调度层数据，默认获取，为null时自动获取控制器当前路由，默认为null
     * @param null $theme 主题名，用于指定获取哪个主题的调度数据
     *
     * @return Dispatch
     */
    public function getDispatch($route = null, $theme = null)
    {
        return Wc::$service->getDispatch()->get($route, $theme);
    }

    /**
     * 执行调度
     *
     * 用法：
     * 假设场景：当前控制器为SiteController()，包含动作actionIndex()、actionTest()
     * ```php
     *      // 1)在actionIndex()方法里直接执行该动作所属的调度，可使用如下代码：
     *      return $this->runDispatch();
     *      // 2)如果需要执行其他动作所属的调度，填写上调度路由即可，如：
     *      return $this->runDispatch('test');
     *      // 3)如果需要跨模块执行相应调度，代码如下:
     *      return $this->runDispatch('passport/common/login');
     *
     *      以上用法等同于：
     *
     *      // 1)
     *      // 获取调度类后再执行run()方法
     *      return $this->getDispatch()->run();
     *      // 直接用调度服务类执行run()方法
     *      return \wocenter\Wc::$service->getDispatch->get()->run();
     *      // 2)
     *      return $this->getDispatch('test')->run();
     *      return \wocenter\Wc::$service->getDispatch->get('test')->run();
     *      // 3)
     *      return $this->getDispatch('passport/common/login')->run();
     *      return \wocenter\Wc::$service->getDispatch->get('passport/common/login')->run();
     * ```
     *
     * @param null $route 路由信息，用于指定获取哪个调度层数据，为null时自动获取控制器当前路由，默认为null
     * @param null $theme 主题名，用于指定获取哪个主题的调度数据
     *
     * @return mixed
     */
    public function runDispatch($route = null, $theme = null)
    {
        return Wc::$service->getDispatch()->run($route, $theme);
    }

    /**
     * 操作失败后返回结果至客户端
     *
     * @param string|array $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     * @param null $theme
     */
    public function error($message = '', $jumpUrl = '', $data = [], $theme = null)
    {
        return $this->getDispatch(null, $theme)->error($message, $jumpUrl, $data);
    }

    /**
     * 操作成功后返回结果至客户端
     *
     * @param string|array $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为1妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     * @param null $theme
     */
    public function success($message = '', $jumpUrl = '', $data = [], $theme = null)
    {
        return $this->getDispatch(null, $theme)->success($message, $jumpUrl, $data);
    }

    /**
     * 显示页面，只支持渲染当前控制器所属模块内的视图模板文件，无法跨模块调用，
     * 如果需要可使用[[$this->getDispatch()]]方法指定要获取的调度器再执行该调度器所属的[[display{}]]方法
     *
     * @param string|null $view
     * @param array $assign
     * @param null $theme
     *
     * @return string|\yii\web\Response
     */
    public function display($view = null, $assign = [], $theme = null)
    {
        return $this->getDispatch(null, $theme)->display($view, $assign);
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

    /**
     * 设置Controller和Dispatch之间需要传递的数据，如同View组件的`params`
     *
     * 使用该方法可向当前控制器的调度器内传入公共数据
     *
     * @param string|array $key
     * @param string|null $value
     *
     * @return Dispatch
     */
    public function setParams($key, $value = null)
    {
        return $this->getDispatch()->setParams($key, $value);
    }

}