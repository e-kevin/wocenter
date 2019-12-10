<?php

namespace wocenter\interfaces;

use wocenter\core\Dispatch;
use yii\base\InvalidRouteException;

/**
 * 系统调度功能（Dispatch）管理接口类
 *
 * 调度管理类支持控制器内[[$dispatchMap]]和[[$defaultDispatches]]属性的配置，但调度管理类的调度配置数据优先级最高。
 * 即调度管理类的配置属于全局配置，会覆盖当前控制器内的[[$dispatchMap]]和[[$defaultDispatches]]属性配置。
 *
 * 约定：
 *  - 系统扩展控制器，位于'@extensions'目录下的控制器
 *  - 开发者控制器，位于'@developer'目录下的控制器
 *  - 用户自定义控制器，位于任何地方，如'@backend/controllers'、'@frontend/controllers'
 *  - 必须配置调度功能所需的params参数，该参数一般在'@app/config/params-local.php'等文件处设置。一般该参数在
 * 安装主题扩展时由系统自动生成配置，可参考以下配置：
 * @see ThemeInfo::getConfig()['params']['themeConfig']
 *
 * @property array $config
 * @property array $dispatchMap
 * @property string $viewPath
 * @property array $themeConfig
 * @property array $runningExtension
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface DispatchManagerInterface
{
    
    /**
     * 获取当前控制器的调度器配置
     * - 如果不存在用户自定义配置数据[[Controller::$dispatchMap]]或全局配置里不存在用户自定义配置数据，
     *   则返回控制器的默认调度器配置信息[[Controller::$defaultDispatches]]，
     *   该属性在初始化阶段[[init()]]已赋值给[[$_defaultDispatches]]。
     * - 如果存在用户自定义配置数据[[Controller::$dispatchMap]]或全局配置里不存在用户自定义配置数据，
     *   则进一步验证配置数据是否规范。
     *
     * @return array
     */
    public function getDispatchMap(): array;
    
    /**
     * 获取全局调度器配置
     *
     * @return array
     */
    public function getConfig(): array;
    
    /**
     * 设置全局调度器配置
     *
     * @param array $config 调度器配置数据
     *
     * @return array
     */
    public function setConfig(array $config): array;
    
    /**
     * 根据路由地址获取调度器，最经常使用的场景是在操作完成后，返回执行结果数据或视图
     * 赋值数据给前端，或获取指定调度器后，使用调度器内某些方法。
     *
     * @param null|string $route 调度路由，支持以下格式：'view', 'config-manager/view'，'system/config-manager/view'
     *
     * @return null|Dispatch|\wocenter\core\web\Dispatch
     * @throws InvalidRouteException
     */
    public function getDispatch($route = null);
    
    /**
     * 创建调度器
     *
     * @param string $id 调度器ID
     *
     * @return null|Dispatch
     */
    public function createDispatch($id);
    
    /**
     * 获取调度器需要渲染的视图文件路径
     *
     * @return string
     */
    public function getViewPath(): string;
    
    /**
     * 获取当前主题的参数配置
     *
     * @return array
     */
    public function getThemeConfig(): array;
    
    /**
     * 当前控制器所属的扩展信息
     *
     * @return RunningExtensionInterface
     */
    public function getRunningExtension();
    
}
