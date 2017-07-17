<?php
namespace wocenter\traits;

/**
 * Class ExtendModelTrait
 * 拓展\wocenter\core\Model, \wocenter\core\ActiveRecord类
 *
 * @property boolean $throwException
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ExtendModelTrait
{

    /**
     * @var string $message 提示消息
     */
    public $message = '';

    /**
     * 抛出异常
     *
     * @var boolean
     */
    protected $_throwException;

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
     * 抛出异常
     *
     * @param boolean $throw
     *
     * @return $this
     */
    public function throwException($throw = true)
    {
        $this->_throwException = $throw;

        return $this;
    }

}
