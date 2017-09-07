<?php
namespace wocenter\core;

use wocenter\interfaces\DispatchInterface;
use wocenter\Wc;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * 系统调度器的基础实现类
 *
 * @method run() 执行调度
 *
 * @package wocenter\core
 * @author E-Kevin <e-kevin@qq.com>
 */
class Dispatch extends Action implements DispatchInterface
{

    /**
     * @var Controller
     */
    public $controller;

    /**
     * @var array 保存视图模板文件赋值数据
     */
    protected $_assign = [];

    /**
     * @inheritdoc
     */
    public function success($message = '', $jumpUrl = '', $data = [])
    {
        Wc::setSuccessMessage($message);

        return $this->controller->redirect($jumpUrl);
    }

    /**
     * @inheritdoc
     */
    public function error($message = '', $jumpUrl = '', $data = [])
    {
        Wc::setErrorMessage($message);

        return $this->controller->redirect($jumpUrl);
    }

    /**
     * @inheritdoc
     */
    public function display($view = null, $assign = [])
    {
        // 没有指定渲染的视图文件名，则默认渲染当前调度器ID的视图文件
        $view = $view ?: $this->id;
        $assign = array_merge($this->_assign, $assign);

        return $this->controller->render($view, $assign);
    }

    /**
     * @inheritdoc
     */
    public function assign($key, $value = null)
    {
        if (is_array($key)) {
            $this->_assign = ArrayHelper::merge($this->_assign, $key);
        } else {
            $this->_assign[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isFullPageLoad($request = null)
    {
        if ($request == null) {
            $request = Yii::$app->getRequest();
        }

        return $request->getIsPjax() || (!$request->getIsAjax() && $request->getIsGet());
    }

    /**
     * @inheritdoc
     */
    public function runWithParams($params)
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');
        }
        $args = $this->controller->bindActionParams($this, $params);
        Yii::trace('Running dispatch: ' . get_class($this) . '::run()', __METHOD__);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }
        if ($this->beforeRun()) {
            $result = call_user_func_array([$this, 'run'], $args);
            $this->afterRun();

            return $result;
        } else {
            return null;
        }
    }

}
