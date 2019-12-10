<?php

namespace wocenter\core;

use Closure;
use wocenter\helpers\ArrayHelper;
use wocenter\interfaces\DispatchManagerInterface;
use wocenter\interfaces\RunningExtensionInterface;
use wocenter\traits\DispatchTrait;
use Yii;
use yii\{
    base\BaseObject, base\Controller, base\ViewContextInterface, caching\Dependency, helpers\VarDumper
};

/**
 * Class BaseWc
 *
 * @property RunningExtensionInterface $runningExtension
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class BaseWc extends BaseObject
{
    
    /**
     * @var ServiceLocator 服务类实例，用于调用系统服务
     */
    public static $service;
    
    /**
     * Wc constructor.
     *
     * @param ServiceLocator $service
     * @param array $config
     *
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function __construct(ServiceLocator $service, $config = [])
    {
        static::$service = $service;
        
        parent::__construct($config);
    }
    
    /**
     * WoCenter 当前版本
     *
     * @return string
     */
    public static function getVersion()
    {
        return '0.4';
    }
    
    /**
     * 输出调试信息
     *
     * @param string|array $var
     * @param string $category
     */
    public static function traceInfo($var, $category = 'Wc::traceInfo')
    {
        Yii::debug(VarDumper::dumpAsString($var), $category);
    }
    
    /**
     * 浏览器友好的变量输出
     *
     * @param mixed $arr 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     *
     * @return void|string
     */
    public static function dump($arr, $echo = true, $label = null, $strict = true)
    {
        ArrayHelper::dump($arr, $echo, $label, $strict);
    }
    
    /**
     * 扩展[[Yii::$app->getCache()->getOrSet()]]该方法，当`$duration`为`false`时先删除缓存再缓存执行数据结果
     *
     * @param mixed $key a key identifying the value to be cached. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @param callable|Closure $callable the callable or closure that will be used to generate a value to be cached.
     * In case $callable returns `false`, the value will not be cached.
     * @param int $duration default duration in seconds before the cache will expire. If not set,
     * [[defaultDuration]] value will be used.
     * @param Dependency|null $dependency dependency of the cached item. If the dependency changes,
     * the corresponding value in the cache will be invalidated when it is fetched via [[get()]].
     * This parameter is ignored if [[serializer]] is `false`.
     * @param string $cache cache component
     *
     * @see \yii\caching\Cache::getOrSet()
     * @return mixed result of $callable execution
     */
    public static function getOrSet($key, $callable, $duration = null, $dependency = null, $cache = 'cache')
    {
        /** @var \yii\caching\Cache $component */
        $component = Yii::$app->get($cache);
        if ($duration === false) {
            $component->delete($key);
        }
        
        return $component->getOrSet($key, $callable, $duration, $dependency);
    }
    
    /**
     * 支持抛出模型类（Model|ActiveRecord）验证错误的事务操作
     *
     * 事务操作默认只抛出异常错误，如果需要抛出模型类产生的验证错误，`$callback`函数内需要被获取到的模型类必须使用
     * [[traits\ExtendModelTrait()]]用以支持该方法
     *
     * @param callable $callback a valid PHP callback that performs the job. Accepts connection instance as parameter.
     * @param string|null $isolationLevel The isolation level to use for this transaction.
     *
     * @throws \Exception
     * @return mixed result of callback function
     */
    public static function transaction(callable $callback, $isolationLevel = null)
    {
        self::setThrowException();
        $result = Yii::$app->getDb()->transaction($callback, $isolationLevel);
        self::setThrowException(false);
        
        return $result;
    }
    
    /**
     * 抛出异常，默认不抛出
     *
     * @var boolean
     */
    protected static $_throwException = false;
    
    /**
     * 获取是否允许抛出异常
     *
     * @return boolean
     */
    public static function getThrowException()
    {
        return static::$_throwException;
    }
    
    /**
     * 设置是否允许抛出异常，默认为`true`(允许)
     *
     * @param boolean $throw
     */
    public static function setThrowException($throw = true)
    {
        static::$_throwException = $throw;
    }
    
    /**
     * 公共缓存类，主要是缓存一些公用的数据
     *
     * @return \yii\caching\Cache|object|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function cache()
    {
        return Yii::$app->get('commonCache');
    }
    
    /**
     * 获取调度管理器
     *
     * @param Controller|DispatchTrait $controller
     * @param array $defaultDispatches
     *
     * @return object|DispatchManagerInterface
     */
    public static function getDispatchManager($controller, array $defaultDispatches)
    {
        return Yii::$container->get(
            Yii::$container->has('wocenter\interfaces\DispatchManagerInterface') ?
                'wocenter\interfaces\DispatchManagerInterface' :
                'wocenter\core\DispatchManager',
            [
                $controller,
                $defaultDispatches,
            ]
        );
    }
    
    /**
     * 当前控制器所属的扩展信息
     *
     * @param Controller|DispatchTrait|ViewContextInterface $controller
     *
     * @return object|RunningExtensionInterface
     */
    public static function getRunningExtension(Controller $controller)
    {
        if (Yii::$container->has(RunningExtensionInterface::class)) {
            $runningExtensionClass = RunningExtensionInterface::class;
        } else {
            $runningExtensionClass = WoCenterExtension::class;
        }
        
        return Yii::$container->get($runningExtensionClass, [$controller]);
    }
    
    /**
     * 获取当前主题的参数配置
     *
     * @param Controller $controller
     *
     * @see \wocenter\core\Theme::getThemeConfig()
     * @return array
     */
    public static function getThemeConfig(Controller $controller): array
    {
        /** @var Theme $theme */
        $theme = $controller->getView()->theme instanceof Theme ?
            $controller->getView()->theme :
            Yii::createObject(Theme::class);
        
        return $theme->getThemeConfig();
    }
    
}
