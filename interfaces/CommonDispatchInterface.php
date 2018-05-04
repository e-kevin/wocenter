<?php

namespace wocenter\interfaces;

use wocenter\core\Dispatch;

/**
 * 主题公共调度器接口类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface CommonDispatchInterface
{
    
    /**
     * 获取主题公共调度器
     *
     * @return null|Dispatch
     */
    public function getCommonDispatch();
    
    /**
     * 初始化调度器运行环境
     */
    public function initDispatchEnvironment();
    
}
