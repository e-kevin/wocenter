<?php

namespace wocenter\enums;

use Yii;

/**
 * 是否枚举
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class YesEnum extends Enums
{
    
    const NO = 0;
    const YES = 1;
    
    /**
     * @inheritdoc
     */
    public static function list()
    {
        return [
            self::NO => Yii::t('wocenter/app', 'No'),
            self::YES => Yii::t('wocenter/app', 'Yes'),
        ];
    }
    
}
