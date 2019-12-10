<?php

namespace wocenter\core\web;

use wocenter\traits\DispatchTrait;
use wocenter\core\Dispatch as baseDispatch;
use Yii\web\Controller;
use yii\web\Response;

/**
 * 系统调度器的基础实现类
 *
 * @method Response success($message = '', $jumpUrl = '', $data = [])
 * @method Response error($message = '', $jumpUrl = '', $data = [])
 * @method string display($view = null, array $setAssign = [])
 * @method Dispatch setAssign($key, $value = null)
 * @method array getAssign()
 * @property array $assign
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Dispatch extends baseDispatch
{
    
    /**
     * @var Controller|DispatchTrait
     */
    public $controller;
    
    public function behaviors()
    {
        return [
            'dispatch' => [
                'class' => 'wocenter\behaviors\DispatchBehavior',
            ],
        ];
    }
    
}
