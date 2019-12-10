<?php

namespace wocenter\traits;

/**
 * 控制器调度功能的快捷方法
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait DispatchShortcutTrait
{
    
    use DispatchTrait;
    
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
        return $this->getDispatchManager()->getDispatch()->error($message, $jumpUrl, $data);
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
        return $this->getDispatchManager()->getDispatch()->success($message, $jumpUrl, $data);
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
        return $this->getDispatchManager()->getDispatch()->display($view, $assign);
    }
    
    /**
     * 保存视图模板文件赋值数据
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
     * @param string|array $value
     *
     * @return self
     */
    public function assign($key, $value = null)
    {
        $this->getDispatchManager()->getDispatch()->assign($key, $value);
        
        return $this;
    }
    
}
