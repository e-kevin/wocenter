<?php

namespace wocenter\interfaces;

/**
 * 枚举接口类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface EnumInterface
{
    
    /**
     * @var integer 不限
     */
    const UNLIMITED = 999;
    
    /**
     * 获取列表
     *
     * @return array
     */
    public static function list();
    
    /**
     * 获取值
     *
     * @param string|\Closure|array $key
     *
     * @return mixed
     */
    public static function value($key);
    
}
