<?php

namespace wocenter\core;

use wocenter\traits\DispatchTrait;
use Yii;
use yii\web\Controller as baseController;

/**
 * 基础Controller类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Controller extends baseController
{
    
    use DispatchTrait;
    
}
