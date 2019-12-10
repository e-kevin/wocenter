<?php

namespace wocenter\core;

use wocenter\traits\DispatchTrait;
use Yii;
use yii\{
    base\Action, base\InvalidConfigException
};
use yii\base\Controller;

/**
 * 系统调度器的基础实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Dispatch extends Action
{
    
    /**
     * @var Controller|DispatchTrait
     */
    public $controller;
    
    /**
     * @inheritdoc
     */
    public function runWithParams($params)
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');
        }
        $args = $this->controller->bindActionParams($this, $params);
        Yii::debug('Running dispatch: ' . get_class($this) . '::run(), invoked by '  . get_class($this->controller), __METHOD__);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }
        if ($this->beforeRun()) {
            $result = call_user_func_array([$this, 'run'], $args);
            $this->afterRun();
            
            return $result;
        } else {
            return null;
        }
    }
    
}
