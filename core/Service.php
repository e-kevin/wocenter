<?php

namespace wocenter\core;

use wocenter\interfaces\ServiceInterface;
use Yii;
use yii\base\{
    InvalidConfigException, BaseObject
};

/**
 * 系统服务实现类
 *
 * @property mixed $info 服务类相关信息
 * @property mixed $data 服务类相关数据
 * @property array $result 服务类执行结果
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
abstract class Service extends BaseObject implements ServiceInterface
{
    
    /**
     * @var Service 当前服务的父级服务，默认为null，即当前服务为顶级服务
     */
    public $service;
    
    /**
     * @var boolean 是否禁用服务类功能，默认不禁用
     */
    public $disabled = false;
    
    /**
     * @var Service[]|array 已经实例化的子服务单例
     */
    private $_subService;
    
    /**
     * @var mixed 服务类相关信息
     */
    protected $_info = '';
    
    /**
     * @var mixed 服务类相关数据
     */
    protected $_data = '';
    
    /**
     * @var boolean 服务类执行状态结果，默认为false
     */
    protected $_status = false;
    
    /**
     * @inheritdoc
     */
    abstract public function getId();
    
    /**
     * 获取子服务
     *
     * 该方法不设为`public`类型，目的在于规范代码，使服务方法对IDE提供友好支持。故所属的子服务类必须以`public`
     * 类型新建方法获取。
     *
     * @param string $serviceName 子服务名，不带后缀`Service`
     *
     * @return Service|BaseObject|null
     * @throws InvalidConfigException
     */
    protected function getSubService($serviceName)
    {
        if ($this->_subService[$serviceName] instanceof Service) {
            return $this->_subService[$serviceName];
        } elseif (!isset($this->_subService[$serviceName])) {
            throw new InvalidConfigException("The {$this->getId()}Service required sub service component `{$serviceName}` is not found.");
        } else {
            $uniqueName = $this->getId() . '/' . $serviceName;
            $this->_subService[$serviceName] = Yii::createObject(array_merge($this->_subService[$serviceName], [
                'service' => $this,
            ]));
            if (!$this->_subService[$serviceName] instanceof Service) {
                throw new InvalidConfigException("The required sub service component `{$uniqueName}` must return
                    an object extends `\\wocenter\\core\\Service`.");
            }
            
            Yii::trace('Loading sub service: ' . $uniqueName, __METHOD__);
            
            return $this->_subService[$serviceName];
        }
    }
    
    /**
     * @inheritdoc
     */
    public function setSubService($config)
    {
        $this->_subService = $config;
    }
    
    /**
     * @inheritdoc
     */
    public function getInfo()
    {
        return $this->_info;
    }
    
    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * @inheritdoc
     */
    public function getResult()
    {
        return [
            'status' => $this->_status,
            'info' => $this->getInfo(),
            'data' => $this->getData(),
        ];
    }
    
}
