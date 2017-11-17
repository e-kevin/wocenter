<?php

namespace wocenter\traits;

use wocenter\behaviors\GetMessageBehavior;

/**
 * Class ExtendModelTrait
 * 拓展\wocenter\core\Model, \wocenter\core\ActiveRecord类
 *
 * @property boolean $throwException
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ExtendModelTrait
{
    
    /**
     * @var string $message 反馈消息
     */
    public $message = '';
    
    /**
     * 抛出异常
     *
     * @var boolean
     */
    protected $_throwException;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            GetMessageBehavior::className(),
        ];
    }
    
    /**
     * 获取是否允许抛出异常
     *
     * @return boolean
     */
    public function getThrowException()
    {
        return $this->_throwException;
    }
    
    /**
     * 设置是否允许抛出异常，默认允许抛出异常
     *
     * @param boolean $throw
     *
     * @return $this
     */
    public function setThrowException($throw = true)
    {
        $this->_throwException = $throw;
        
        return $this;
    }
    
    /**
     * 获取模型所有数据，通常结合缓存使用
     *
     * @return array
     */
    public function getAll()
    {
        return [];
    }
    
}
