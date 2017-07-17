<?php
namespace wocenter\core;

use Exception;
use Yii;
use wocenter\interfaces\ServiceInterface;
use yii\base\InvalidConfigException;
use yii\base\Object;

/**
 * 系统服务实现类
 *
 * @property mixed $info 服务类相关信息
 * @property mixed $data 服务类相关数据
 * @property array $result 服务类执行结果
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
abstract class Service extends Object implements ServiceInterface
{

    /**
     * @var Service 当前服务的父级服务，默认为null，即当前服务为父级服务
     */
    public $service;

    /**
     * @var boolean 是否禁用服务类功能，默认不禁用
     */
    public $disabled = false;

    /**
     * @var Service[] 已经实例化的子服务单例
     */
    private $_subService;

    /**
     * @var string|array 服务类相关信息
     */
    protected $_info = '';

    /**
     * @var string|array 服务类相关数据
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
     * @param string $serviceName 子服务名，不带后缀`Service`
     *
     * @return Service[]
     * @throws InvalidConfigException
     */
    protected function getSubService($serviceName)
    {
        $uniqueName = $this->getId() . '/' . $serviceName;
        if (!$this->_subService[$uniqueName] instanceof Service) {
            Yii::trace('Loading sub service: ' . $uniqueName, __METHOD__);

            $this->_subService[$uniqueName] = Yii::createObject(array_merge($this->_subService[$uniqueName], [
                'service' => $this,
            ]));
            if (!$this->_subService[$uniqueName] instanceof Service) {

                throw new InvalidConfigException("The required sub service component `{$uniqueName}` must return an object extends `\\wocenter\\core\\Service`.");
            }
        }

        return $this->_subService[$uniqueName];
    }

    /**
     * 设置子服务
     *
     * @param string|array|callable $config 子服务配置信息
     *
     * @see \yii\BaseYii::createObject()
     * @see \wocenter\core\ServiceLocator::loadServiceConfig()
     */
    public function setSubService($config)
    {
        $this->_subService = $config;
    }

    /**
     * 服务类执行后的相关信息
     *
     * @return string
     * @throws Exception
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * 服务类执行后的相关数据
     *
     * @return array|string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 服务类执行后的结果数组
     *
     * @return array ['status', 'info', 'data']
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
