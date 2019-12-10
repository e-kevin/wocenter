<?php

namespace wocenter\enums;

use Yii;

/**
 * 启用状态枚举
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class EnableEnum extends Enums
{
    
    const DISABLE = 0;
    const ENABLE = 1;
    
    /**
     * @inheritdoc
     */
    public static function list()
    {
        return [
            self::DISABLE => Yii::t('wocenter/app', 'Disable'),
            self::ENABLE => Yii::t('wocenter/app', 'Enable'),
        ];
    }
    
}
