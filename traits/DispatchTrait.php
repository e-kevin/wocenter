<?php

namespace wocenter\traits;

use wocenter\core\Dispatch;
use wocenter\core\DispatchManager;
use wocenter\core\Modularity;
use wocenter\interfaces\DispatchManagerInterface;
use wocenter\Wc;
use yii\base\Action;

/**
 * 让Controller控制器类支持系统（Dispatch）调度功能
 *
 * @property DispatchManagerInterface $dispatchManager 调度器管理器
 * @property array $extensionConfig 当前扩展配置信息
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait DispatchTrait
{
    
    /**
     * @var Modularity
     */
    public $module;
    
    /**
     * @var Dispatch
     */
    public $action;
    
    /**
     * @var int 运行模式
     * @see DispatchManager::$runMode // fixme
     */
    public $runMode;
    
    /**
     * @return array 调度器配置
     * @see DispatchManagerInterface::getDispatchMap()
     */
    public $dispatchMap;
    
    /**
     * @var array 默认调度器配置
     * @see DispatchManager::$_defaultDispatches // fixme
     */
    protected $defaultDispatches = [];
    
    /**
     * @inheritdoc
     *
     * @param string $id
     *
     * @return null|Action|Dispatch
     */
    public function createAction($id)
    {
        return parent::createAction($id) ?: $this->getDispatchManager()->createDispatch($id);
    }
    
    /**
     * @var DispatchManagerInterface
     */
    private $_dispatchManager;
    
    /**
     * 获取调度管理器
     *
     * @return object|DispatchManagerInterface
     */
    public function getDispatchManager()
    {
        if (null === $this->_dispatchManager) {
            $this->_dispatchManager = Wc::getDispatchManager($this, $this->defaultDispatches);
        }
        
        return $this->_dispatchManager;
    }
    
}
