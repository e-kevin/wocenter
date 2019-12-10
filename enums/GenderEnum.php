<?php

namespace wocenter\enums;

use Yii;

/**
 * 性别枚举
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class GenderEnum extends Enums
{
    
    const UNKNOWN = 0;
    const MALE = 1;
    const FEMALE = 2;
    
    /**
     * @inheritdoc
     */
    public static function list()
    {
        return [
            self::UNKNOWN => Yii::t('wocenter/app', 'Secrecy'),
            self::MAN => Yii::t('wocenter/app', 'Male'),
            self::WOMAN => Yii::t('wocenter/app', 'Female'),
        ];
    }
    
}
