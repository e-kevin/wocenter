<?php

namespace wocenter\interfaces;

/**
 * 主题扩展详情接口类
 *
 * @property string $viewPath 主题视图路径
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ThemeInfoInterface
{
    
    /**
     * 获取主题视图路径
     *
     * @return string
     */
    public function getViewPath();
    
    /**
     * 设置主题视图路径
     *
     * @param string $viewPath 主题视图路径
     */
    public function setViewPath($viewPath);
    
}
