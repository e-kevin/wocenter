<?php

namespace wocenter\enums;

use Yii;

/**
 * 显隐状态枚举
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class VisibleEnum extends Enums
{
    
    const INVISIBLE = 0;
    const VISIBLE = 1;
    
    /**
     * @inheritdoc
     */
    public static function list()
    {
        return [
            self::INVISIBLE => Yii::t('wocenter/app', 'Hidden'),
            self::VISIBLE => Yii::t('wocenter/app', 'Display'),
        ];
    }
    
}
