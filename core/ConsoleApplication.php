<?php

namespace wocenter\core;

use wocenter\traits\ApplicationTrait;
use yii\console\Application as baseApplication;

/**
 * 控制台基础Application类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ConsoleApplication extends baseApplication
{
    
    use ApplicationTrait;
    
}
