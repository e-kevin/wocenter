<?php

namespace wocenter\traits;

use wocenter\behaviors\CatchMessageBehavior;

/**
 * Class ExtendModelTrait
 *
 * @property int|false $cacheDuration
 * @property boolean $throwException
 * @property array $all
 * @property string $_message
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ExtendModelTrait
{
    
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
        return array_merge(parent::behaviors(), [
            CatchMessageBehavior::class,
        ]);
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
    
    /**
     * 清除缓存
     */
    public function clearCache()
    {
    }
    
    /**
     * @var integer|false 缓存时间间隔
     */
    private $_cacheDuration;
    
    /**
     * 获取缓存时间间隔
     *
     * @return false|int
     */
    public function getCacheDuration()
    {
        if (null === $this->_cacheDuration) {
            $this->setCacheDuration();
        }
        
        return $this->_cacheDuration;
    }
    
    /**
     * 设置缓存时间间隔
     *
     * @param false|int $cacheDuration 当为`false`时，则删除缓存数据，默认缓存`一天`
     *
     * @return $this
     */
    public function setCacheDuration($cacheDuration = 86400)
    {
        $this->_cacheDuration = $cacheDuration;
        
        return $this;
    }
    
}
