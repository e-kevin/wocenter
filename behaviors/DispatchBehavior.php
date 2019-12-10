<?php

namespace wocenter\behaviors;

use wocenter\core\web\Dispatch;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

/**
 * 调度器行为类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DispatchBehavior extends Behavior
{
    
    /**
     * @var Dispatch
     */
    public $owner;
    
    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        if (!$owner instanceof Dispatch) {
            throw new InvalidConfigException('The owner of this behavior `' . self::class . '` must be instanceof ' .
                '`\wocenter\core\web\Dispatch`');
        }
        parent::attach($owner);
    }
    
    /**
     * 操作成功后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array|null $jumpUrl 页面跳转地址。默认为`null`，即仅设置提示信息。
     *  - string: 当为字符串时，则自动跳转到该地址，为空字符串时则跳转到当前请求地址。
     *  - array: 数组形式的路由地址。
     *  - null: 不需要跳转。
     * @param mixed $data 返回给客户端的数据。
     * 该值设置建议遵照以下原则：
     *  - 为整数，代表页面跳转停留时间，默认为1妙，时间结束后自动跳转至指定的`$jumpUrl`页面。
     *  - 为数组，代表返回给客户端的数据。
     *
     * @return Response
     */
    public function success($message = '', $jumpUrl = null, $data = []): Response
    {
        return $this->_dispatchJump($message ?: Yii::t('wocenter/app', 'Operation successful.'), 1, $jumpUrl, $data);
    }
    
    /**
     * 操作失败后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array|null $jumpUrl 页面跳转地址。默认为`null`，即仅设置提示信息。
     *  - string: 当为字符串时，则自动跳转到该地址，为空字符串时则跳转到当前请求地址。
     *  - array: 数组形式的路由地址。
     *  - null: 不需要跳转。
     * @param mixed $data 返回给客户端的数据。
     * 该值设置建议遵照以下原则：
     *  - 为整数，代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面。
     *  - 为数组，代表返回给客户端的数据。
     *
     * @return Response
     */
    public function error($message = '', $jumpUrl = null, $data = []): Response
    {
        return $this->_dispatchJump($message ?: Yii::t('wocenter/app', 'Operation failure.'), 0, $jumpUrl, $data);
    }
    
    /**
     * 处理跳转操作，支持错误跳转和正确跳转。
     * 主要是把结果数据以json形式返回给前端，一般用来处理AJAX请求
     *
     * @param string|array $message 提示信息
     * @param integer $status 状态 1:success 0:error
     * @param string|array|null $jumpUrl 页面跳转地址。默认为`null`，即仅设置提示信息。
     *  - string: 当为字符串时，则自动跳转到该地址，为空字符串时则跳转到当前请求地址。
     *  - array: 数组形式的路由地址。
     *  - null: 不需要跳转。
     * @param array|integer $data
     *
     * @return Response 默认都会返回包含以下键名的数据到客户端
     * ```php
     * [
     *      'waitSecond',
     *      'jumpUrl',
     *      'message',
     *      'status',
     *      ...,
     * ]
     * ```
     */
    protected function _dispatchJump($message = '', $status = 1, $jumpUrl = null, $data = []): Response
    {
        $request = Yii::$app->getRequest();
        // 设置跳转时间
        if (is_int($data)) {
            $params['waitSecond'] = $data;
        } elseif (is_array($data)) {
            $params = $data;
            if (!isset($params['waitSecond'])) {
                $params['waitSecond'] = $status ? 1 : 3;
            }
        }
        $params['jumpUrl'] = $jumpUrl === null ? '' : Url::to($jumpUrl);
        $params['status'] = $status;
        $params['message'] = $message;
        
        // 全页面加载
        if ($request->getIsPjax() || !$request->getIsAjax()) {
            Yii::$app->getSession()->setFlash($status ? 'success' : 'error', $params['message']);
            if (!empty($params['jumpUrl'])) {
                $this->owner->controller->redirect($params['jumpUrl']);
                Yii::$app->end();
            } else {
                $this->setAssign($params);
            }
        } // AJAX请求方式
        else {
            $this->owner->controller->asJson($params);
            Yii::$app->end();
        }
        
        return Yii::$app->getResponse();
    }
    
    /**
     * 显示页面
     *
     * @param string|null $view 需要渲染的视图文件名
     * @param array $assign 视图模板赋值数据
     *
     * @return string
     */
    public function display($view = null, array $assign = [])
    {
        return $this->setAssign($assign)->controller->render(
            $view ?: $this->owner->controller->getDispatchManager()->getViewPath(),
            $this->getAssign()
        );
    }
    
    /**
     * @var array 保存视图模板文件赋值数据
     */
    protected $_assign = [];
    
    /**
     * 设置视图模板文件赋值数据
     *
     * 示例：
     * ```php
     *  $this->setAssign('name1', 'apple');
     *  $this->setAssign('name2', 'orange');
     *  等于
     *  $this->setAssign([
     *      'name1' => 'apple',
     *      'name2' => 'orange'
     *  ]);
     *
     * ```
     *
     * 使用该方法可向当前控制器的调度器内传入公共模板数据
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return Dispatch
     */
    final public function setAssign($key, $value = null)
    {
        if (is_array($key)) {
            $this->_assign = ArrayHelper::merge($this->_assign, $key);
        } else {
            $this->_assign[$key] = $value;
        }
        
        return $this->owner;
    }
    
    /**
     * 获取视图模板文件赋值数据
     *
     * @return array
     */
    final public function getAssign()
    {
        return $this->_assign;
    }
    
}