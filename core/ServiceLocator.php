<?php

namespace wocenter\core;

use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\base\UnknownPropertyException;

/**
 * 系统服务定位器，主要作用有：
 * 1. 检测服务组件是否符合WoCenter的服务类标准。
 * 2. 支持IDE代码提示功能，方便开发。
 * 3. 支持`Yii::trace()`调试信息。
 *
 * @property \wocenter\services\AccountService $account
 * @property \wocenter\services\ActionService $action
 * @property \wocenter\services\LogService $log
 * @property \wocenter\services\MenuService $menu
 * @property \wocenter\services\NotificationService $notification
 * @property \wocenter\services\PassportService $passport
 * @property \wocenter\services\SystemService $system
 * @property \wocenter\services\ExtensionService $extension
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
     * @return Service
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
    
    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif ($service = $this->getService($name)) {
            return $service;
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
    
    /**
     * 用户管理服务类
     *
     * @return \wocenter\services\AccountService
     */
    public function getAccount()
    {
        return $this->getService('account');
    }
    
    /**
     * 系统服务类
     *
     * @return \wocenter\services\SystemService
     */
    public function getSystem()
    {
        return $this->getService('system');
    }
    
    /**
     * 行为管理服务类
     *
     * @return \wocenter\services\ActionService
     */
    public function getAction()
    {
        return $this->getService('action');
    }
    
    /**
     * 日志管理服务类
     *
     * @return \wocenter\services\LogService
     */
    public function getLog()
    {
        return $this->getService('log');
    }
    
    /**
     * 系统通知服务类
     *
     * @return \wocenter\services\NotificationService
     */
    public function getNotification()
    {
        return $this->getService('notification');
    }
    
    /**
     * 系统通行证服务类
     *
     * @return \wocenter\services\PassportService
     */
    public function getPassport()
    {
        return $this->getService('passport');
    }
    
    /**
     * 菜单管理服务类
     *
     * @return \wocenter\services\MenuService
     */
    public function getMenu()
    {
        return $this->getService('menu');
    }
    
    /**
     * 系统扩展服务类
     *
     * @return \wocenter\services\ExtensionService
     */
    public function getExtension()
    {
        return $this->getService('extension');
    }
    
}
