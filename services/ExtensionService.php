<?php

namespace wocenter\services;

use wocenter\core\Service;

/**
 * 系统扩展服务类
 *
 * @property \wocenter\services\extension\ControllerService $controller
 * @property \wocenter\services\extension\ModularityService $modularity
 * @property \wocenter\services\extension\LoadService $load
 * @property \wocenter\services\extension\ThemeService $theme
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ExtensionService extends Service
{
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'extension';
    }
    
    /**
     * 获取功能扩展子服务类
     *
     * @return \wocenter\services\extension\ControllerService|Service
     */
    public function getController()
    {
        return $this->getSubService('controller');
    }
    
    /**
     * 管理系统模块子服务类
     *
     * @return \wocenter\services\extension\ModularityService|Service
     */
    public function getModularity()
    {
        return $this->getSubService('modularity');
    }
    
    /**
     * 加载扩展子服务类
     *
     * @return \wocenter\services\extension\LoadService|Service
     */
    public function getLoad()
    {
        return $this->getSubService('load');
    }
    
    /**
     * 管理系统主题子服务类
     *
     * @return \wocenter\services\extension\ThemeService|Service
     */
    public function getTheme()
    {
        return $this->getSubService('theme');
    }
    
}