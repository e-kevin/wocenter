<?php
namespace wocenter\interfaces;

/**
 * 系统服务接口类
 *
 * @package wocenter\interfaces
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ServiceInterface
{

    /**
     * @return string 服务ID
     */
    public function getId();

    /**
     * 设置子服务
     *
     * @param string|array|callable $config 子服务配置信息
     * @see \yii\BaseYii::createObject()
     * @see \wocenter\core\ServiceLocator::loadServiceConfig()
     */
    public function setSubService($config);

    /**
     * 服务类执行后的相关信息
     *
     * @return string
     */
    public function getInfo();

    /**
     * 服务类执行后的相关数据
     *
     * @return array|string
     */
    public function getData();

    /**
     * 服务类执行后的结果数组
     *
     * @return array ['status', 'info', 'data']
     */
    public function getResult();

}
