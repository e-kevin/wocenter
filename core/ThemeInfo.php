<?php

namespace wocenter\core;

use wocenter\interfaces\ThemeInfoInterface;

/**
 * 主题扩展信息类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ThemeInfo extends Extension implements ThemeInfoInterface
{
    
    /**
     * 获取主题公共调度器
     *
     * @return string
     */
    public $dispatch = '\wocenter\core\Dispatch';
    
    /**
     * @var string 主题视图路径
     */
    private $_viewPath;
    
    /**
     * 获取主题视图路径
     *
     * @return string
     */
    public function getViewPath()
    {
        return $this->_viewPath;
    }
    
    /**
     * 设置主题视图路径
     *
     * @param string $viewPath 主题视图路径
     */
    public function setViewPath($viewPath)
    {
        $this->_viewPath = $viewPath;
    }
    
}
