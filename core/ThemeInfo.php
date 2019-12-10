<?php

namespace wocenter\core;

use wocenter\interfaces\ThemeInfoInterface;

/**
 * 主题扩展信息类
 *
 * @property string $viewPath
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ThemeInfo extends ExtensionInfo implements ThemeInfoInterface
{
    
    /**
     * 主题公共调度器行为类
     *
     * @return string
     */
    public $dispatch = '\wocenter\behaviors\DispatchBehavior';
    
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
    
    /**
     * @inheritdoc
     *
     * - 添加对扩展目录和开发者目录视图的映射
     * - 添加默认的主题参数配置
     * @see \wocenter\core\Theme::getThemeConfig()
     *
     */
    public function getConfig()
    {
        return [
            'components' => [
                'view' => [
                    'theme' => [
                        'class' => '\wocenter\core\Theme',
                    ],
                ],
            ],
            'params' => [
                // 添加当前主题配置参数
                'themeConfig' => [
                    'name' => $this->id, // 当前主题ID，如：adminlte、basic。该值决定调度器在哪个主题目录下获取调度器
                    'dispatch' => $this->dispatch, // 当前主题公共调度器
                    'viewPath' => $this->getViewPath(), // 当前主题的视图路径
                ],
            ],
        ];
    }
    
}
