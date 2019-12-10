<?php

namespace wocenter\core;

use Yii;
use yii\base\{
    InvalidConfigException, BaseObject, Module
};

/**
 * 系统服务定位器，主要作用有：
 * 1. 检测服务组件是否符合WoCenter的服务类标准。
 * 2. 支持IDE代码提示功能，方便开发。
 * 3. 支持`Yii::debug()`调试信息。
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ServiceLocator extends BaseObject
{
    
    /**
     * @var Module 获取服务组件的容器
     */
    public $container;
    
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->container = $this->container ?: Yii::$app;
        if (!is_subclass_of($this->container, Module::class)) {
            throw new InvalidConfigException("The `\$container` property must return an object extends `\\yii\\base\\Module`.");
        }
    }
    
    /**
     * 获取系统顶级服务类
     *
     * @param string $serviceName 服务名，不带后缀`ExtensionService`，如：`passport`、`log`
     *
     * @return Service|object|null
     * @throws InvalidConfigException
     */
    public function getService($serviceName)
    {
        $service = $serviceName . 'Service';
        if (!$this->container->has($service, true)) {
            /** @var Service $component */
            $component = $this->container->get($service);
            if (!$component instanceof Service) {
                throw new InvalidConfigException("The required service component `{$service}` must return
                an object extends `\\wocenter\\core\\ExtensionService`.");
            }
            
            if (YII_ENV_DEV) {
                Yii::debug("Loading service: {$service}: {$component->getUniqueId()}", __METHOD__);
            }
            
            return $component;
        } else {
            return $this->container->get($service);
        }
    }
    
}
