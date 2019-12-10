<?php

namespace wocenter\core;

use wocenter\interfaces\ServiceInterface;
use Yii;
use yii\base\{
    InvalidConfigException, BaseObject
};
use yii\helpers\ArrayHelper;

/**
 * 系统服务实现类
 *
 * @property mixed $info 服务类相关信息
 * @property mixed $data 服务类相关数据
 * @property array $result 服务类执行结果
 * @property int|false $cacheDuration 缓存时间间隔
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Service extends BaseObject implements ServiceInterface
{
    
    /**
     * @var string 服务ID
     */
    protected $id;
    
    /**
     * @var Service|null 当前服务的父级服务，默认为null，即当前服务为顶级服务
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
     * @var array 必须设置的属性值
     */
    protected $mustBeSetProps = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!in_array('id', $this->mustBeSetProps)) {
            array_push($this->mustBeSetProps, 'id');
        }
        foreach ($this->mustBeSetProps as $prop) {
            if ($this->{$prop} === null) {
                throw new InvalidConfigException(get_called_class() . ': The `$' . $prop . '` property must be set.');
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getUniqueId()
    {
        return $this->service ? $this->service->getUniqueId() . '/' . $this->id : $this->id;
    }
    
    /**
     * @inheritdoc
     */
    public function coreServices()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function getSubService($serviceName)
    {
        if (null === $this->_subService) {
            $this->setSubService([]);
        }
        if (!isset($this->_subService[$serviceName])) {
            throw new InvalidConfigException("The Service:`{$this->getUniqueId()}` required sub service component `{$serviceName}` is not found.");
        } elseif ($this->_subService[$serviceName] instanceof Service) {
            return $this->_subService[$serviceName];
        } else {
            $uniqueName = $this->getUniqueId() . '/' . $serviceName;
            $this->_subService[$serviceName] = Yii::createObject(array_merge($this->_subService[$serviceName], [
                'service' => $this,
            ]));
            if (!$this->_subService[$serviceName] instanceof Service) {
                throw new InvalidConfigException("The required sub service component `{$uniqueName}` must return an object extends `\\wocenter\\core\\Service`.");
            }
            
            Yii::debug('Loading sub service: ' . $uniqueName, __METHOD__);
            
            return $this->_subService[$serviceName];
        }
    }
    
    /**
     * @inheritdoc
     */
    public function setSubService($config)
    {
        $this->_subService = ArrayHelper::merge($this->coreServices(), $config ?? []);
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
    
    /**
     * @var integer|false 缓存时间间隔
     */
    private $_cacheDuration;
    
    /**
     * @inheritdoc
     */
    public function getCacheDuration()
    {
        if (null === $this->_cacheDuration) {
            if (null !== $this->service) {
                return $this->service->getCacheDuration();
            }
            $this->setCacheDuration();
        }
        
        return $this->_cacheDuration;
    }
    
    /**
     * @inheritdoc
     */
    public function setCacheDuration($cacheDuration = 86400)
    {
        $this->_cacheDuration = $cacheDuration;
        
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
    }
    
}
