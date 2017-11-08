<?php

namespace wocenter\core;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;

/**
 * 系统服务定位器，主要作用有：
 * 1. 检测服务组件是否符合WoCenter的服务类标准。
 * 2. 支持IDE代码提示功能，方便开发。
 * 3. 支持`Yii::trace()`调试信息。
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ServiceLocator extends Object
{
    
    /**
     * 获取系统顶级服务类
     *
     * @param string $serviceName 服务名，不带后缀`Service`，如：`passport`、`log`
     *
     * @return Service|object|null
     * @throws InvalidConfigException
     */
    public function getService($serviceName)
    {
        $service = $serviceName . 'Service';
        if (!Yii::$app->has($service, true)) {
            /** @var Service $component */
            $component = Yii::$app->get($service);
            if (!$component instanceof Service) {
                throw new InvalidConfigException("The required service component `{$service}` must return an object
                    extends `\\wocenter\\core\\Service`.");
            } elseif ($component->getId() != $serviceName) {
                throw new InvalidConfigException("{$component->className()}::getId() method must
                    return the '{$serviceName}' value.");
            }
            
            Yii::trace('Loading service: ' . $serviceName, __METHOD__);
            
            return $component;
        } else {
            return Yii::$app->get($service);
        }
    }
    
}
