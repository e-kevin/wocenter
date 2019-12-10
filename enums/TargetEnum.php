<?php

namespace wocenter\enums;

use Yii;

/**
 * 链接打开方式枚举
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class TargetEnum extends Enums
{
    
    const TARGET_SELF = 0;
    const TARGET_BLANK = 1;
    
    /**
     * @inheritdoc
     */
    public static function list()
    {
        return [
            self::TARGET_SELF => Yii::t('wocenter/app', 'Target self'),
            self::TARGET_BLANK => Yii::t('wocenter/app', 'Target blank'),
        ];
    }
    
}
