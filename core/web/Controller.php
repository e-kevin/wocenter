<?php

namespace wocenter\core\web;

use wocenter\traits\DispatchTrait;
use yii\web\Controller as baseController;

/**
 * 支持系统调度功能（Dispatch）的基础Controller类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Controller extends baseController
{
    
    use DispatchTrait;
    
}
