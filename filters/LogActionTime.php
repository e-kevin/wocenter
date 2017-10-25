<?php

namespace wocenter\filters;

use Yii;
use yii\base\ActionFilter;

/**
 * 记录动作执行时间日志
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class LogActionTime extends ActionFilter
{
    
    private $_startTime;
    
    public function beforeAction($action)
    {
        $this->_startTime = microtime(true);
        
        return parent::beforeAction($action);
    }
    
    public function afterAction($action, $result)
    {
        $time = microtime(true) - $this->_startTime;
        $time = number_format($time, 4);
        Yii::trace("Action '{$action->uniqueId}' spent $time second.");
        
        return parent::afterAction($action, $result);
    }
    
}
