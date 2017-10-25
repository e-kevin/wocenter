<?php

namespace wocenter\interfaces;

/**
 * 菜单接口类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface MenuInterface
{
    
    /**
     * 菜单创建者为用户
     */
    const CREATE_TYPE_BY_USER = 0;
    
    /**
     * 菜单创建者为模块
     */
    const CREATE_TYPE_BY_MODULE = 1;
    
    /**
     * 菜单创建者为扩展
     */
    const CREATE_TYPE_BY_EXTENSION = 2;
    
}
