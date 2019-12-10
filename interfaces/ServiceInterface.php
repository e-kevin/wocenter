<?php

namespace wocenter\interfaces;

use wocenter\core\Service;
use yii\base\InvalidConfigException;

/**
 * 服务接口类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ServiceInterface
{
    
    /**
     * @return string 服务唯一ID
     */
    public function getUniqueId();
    
    /**
     * 核心子服务类
     *
     * @return array
     */
    public function coreServices();
    
    /**
     * 设置子服务
     *
     * @param string|array|callable $config 子服务配置信息
     *
     * @see \yii\BaseYii::createObject()
     */
    public function setSubService($config);
    
    /**
     * 获取子服务
     *
     * @param string $serviceName 子服务名，不带后缀`Service`
     *
     * @return Service|null
     * @throws InvalidConfigException
     */
    public function getSubService($serviceName);
    
    /**
     * 服务类执行后的相关信息
     *
     * @return mixed
     */
    public function getInfo();
    
    /**
     * 服务类执行后的相关数据
     *
     * @return mixed
     */
    public function getData();
    
    /**
     * 服务类执行后的结果数组
     *
     * @return array ['status', 'info', 'data']
     */
    public function getResult();
    
    /**
     * 获取缓存时间间隔
     *
     * @return false|int
     */
    public function getCacheDuration();
    
    /**
     * 设置缓存时间间隔
     *
     * @param false|int $cacheDuration 当为`false`时，则删除缓存数据，默认缓存`一天`
     *
     * @return $this
     */
    public function setCacheDuration($cacheDuration = 86400);
    
    /**
     * 删除缓存
     */
    public function clearCache();
    
}
