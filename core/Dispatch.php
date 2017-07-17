<?php
namespace wocenter\core;

use wocenter\interfaces\DispatchInterface;
use wocenter\Wc;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\web\Request;
use yii\web\Response;

/**
 * Class Dispatch
 */
abstract class Dispatch extends Object implements DispatchInterface
{

    /**
     * @var Controller 调度类当前控制器，主要用于视图渲染等操作
     */
    public $controller;

    /**
     * @var string 调度器默认渲染的视图模板，在DispatchService里默认指定
     * @see \wocenter\services\DispatchService::get()
     */
    public $view;

    /**
     * @var array 传递Controller和Dispatch之间的数据，如同View组件的`params`
     */
    protected $_params = [];

    /**
     * @var array 保存视图模板文件赋值数据
     */
    protected $_assign = [];

    /**
     * 操作成功后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为1妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * 通常自建该方法时，建议在方法最后添加如下代码以防止不必要的输出显示
     * \Yii::$app->end();
     */
    abstract public function success($message = '', $jumpUrl = '', $data = []);

    /**
     * 操作失败后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * 通常自建该方法时，建议在方法最后添加如下代码以防止不必要的输出显示
     * \Yii::$app->end();
     */
    abstract public function error($message = '', $jumpUrl = '', $data = []);

    /**
     * 显示页面
     *
     * @param string|null $view
     * @param array $assign
     *
     * @return string|Response
     */
    abstract public function display($view = null, $assign = []);

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
     * @param string|array $key
     * @param string|array $value
     *
     * @return $this
     */
    public function assign($key, $value = '')
    {
        if (is_array($key)) {
            $this->_assign = ArrayHelper::merge($this->_assign, $key);
        } else {
            $this->_assign[$key] = $value;
        }

        return $this;
    }

    /**
     * 是否全页面加载
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isFullPageLoad($request = null)
    {
        if ($request == null) {
            $request = Yii::$app->getRequest();
        }

        return $request->getIsPjax() || (!$request->getIsAjax() && $request->getIsGet());
    }

    /**
     * 执行调度，返回调度结果
     *
     * @return mixed
     */
    public function run()
    {
        return Wc::$service->getDispatch()->generateRunFile();
    }

    /**
     * 设置Controller和Dispatch之间需要传递的数据，一般是动作控制器需要绑定的参数
     *
     * @param string|array $key
     * @param string|null $value
     *
     * @return $this
     */
    public function setParams($key, $value = null)
    {
        if (is_array($key)) {
            $this->_params = ArrayHelper::merge($this->_params, $key);
        } else {
            $this->_params[$key] = $value;
        }

        return $this;
    }

}