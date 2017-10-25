<?php

namespace wocenter\interfaces;

use yii\web\Request;
use yii\web\Response;

/**
 * 系统调度接口类
 * @author E-Kevin <e-kevin@qq.com>
 */
interface DispatchInterface
{
    
    /**
     * 刷新列表数据操作符，由JS处理
     */
    const RELOAD_LIST = 'js:reload-list';
    
    /**
     * 刷新页面操作符，由JS处理
     */
    const RELOAD_PAGE = 'js:reload-page';
    
    /**
     * 刷新整个页面操作符，由JS处理
     */
    const RELOAD_FULL_PAGE = 'js:reload-full-page';
    
    /**
     * 操作成功后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * @return mixed
     */
    public function success($message = '', $jumpUrl = '', $data = []);
    
    /**
     * 操作失败后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * @return mixed
     */
    public function error($message = '', $jumpUrl = '', $data = []);
    
    /**
     * 显示页面
     *
     * @param string|null $view
     * @param array $assign
     *
     * @return Response
     */
    public function display($view = null, $assign = []);
    
    /**
     * 保存视图模板文件赋值数据
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function assign($key, $value = null);
    
    /**
     * 是否全页面加载
     *
     * @param Request $request
     *
     * @return boolean
     */
    public function isFullPageLoad($request = null);
    
}
