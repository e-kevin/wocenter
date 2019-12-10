<?php

namespace wocenter\enums;

use wocenter\helpers\ArrayHelper;
use wocenter\interfaces\EnumInterface;

/**
 * 枚举类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
abstract class Enums implements EnumInterface
{
    
    /**
     * 获取值
     *
     * @param string|\Closure|array $key
     *
     * @return mixed
     */
    public static function value($key)
    {
        return ArrayHelper::getValue(static::list(), $key);
    }
    
}
