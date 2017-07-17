<?php
namespace wocenter;

use wocenter\core\ServiceLocator;
use wocenter\helpers\ArrayHelper;
use Yii;
use yii\base\Object;
use yii\helpers\VarDumper;

/**
 * Class Wc
 *
 * @package wocenter
 */
class Wc extends Object
{

    /**
     * @var \wocenter\core\ServiceLocator 服务类实例，用于调用系统服务
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
        self::$service = $service;

        parent::__construct($config);
    }

    /**
     * 输出调试信息
     *
     * @param $var
     * @param string $category
     */
    public static function traceInfo($var, $category = 'application')
    {
        Yii::info(VarDumper::dumpAsString($var), $category);
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
        return ArrayHelper::dump($arr, $echo, $label, $strict);
    }

    /**
     * 设置警告闪存信息
     *
     * @param $message
     */
    public static function setWarningMessage($message)
    {
        Yii::$app->getSession()->setFlash('warning', $message);
    }

    /**
     * 设置成功闪存信息
     *
     * @param $message
     */
    public static function setSuccessMessage($message)
    {
        Yii::$app->getSession()->setFlash('success', $message);
    }

    /**
     * 设置错误闪存信息
     *
     * @param $message
     */
    public static function setErrorMessage($message)
    {
        Yii::$app->getSession()->setFlash('error', $message);
    }


    /**
     * 设置提示闪存信息
     *
     * @param $message
     */
    public static function setInfoMessage($message)
    {
        Yii::$app->getSession()->setFlash('info', $message);
    }

    /**
     * Method combines both [[set()]] and [[get()]] methods to retrieve value identified by a $key,
     * or to store the result of $closure execution if there is no cache available for the $key.
     *
     * Usage example:
     *
     * ```php
     * public function getTopProducts($count = 10) {
     *     $cache = $this->cache; // Could be Yii::$app->cache
     *     return $cache->getOrSet(['top-n-products', 'n' => $count], function ($cache) use ($count) {
     *         return Products::find()->mostPopular()->limit(10)->all();
     *     }, 1000);
     * }
     * ```
     *
     * @param mixed $key a key identifying the value to be cached. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @param \Closure $closure the closure that will be used to generate a value to be cached.
     * In case $closure returns `false`, the value will not be cached.
     * @param int|boolean $duration default duration in seconds before the cache will expire. If not set,
     * [[defaultDuration]] value will be used.
     * When the $duration is `false`, empty the current cache.
     * @param \yii\caching\Dependency $dependency dependency of the cached item. If the dependency changes,
     * the corresponding value in the cache will be invalidated when it is fetched via [[get()]].
     * This parameter is ignored if [[serializer]] is `false`.
     *
     * @return mixed result of $closure execution
     * @see \yii\caching\Cache
     * @since 2.0.11
     */
    public static function getOrSet($key, \Closure $closure, $duration = null, $dependency = null)
    {
        if ($duration === false) {
            Yii::$app->getCache()->delete($key);
        }

        return Yii::$app->getCache()->getOrSet($key, $closure, $duration, $dependency);
    }

    /**
     * Executes callback provided in a transaction.
     *
     * @param callable $callback a valid PHP callback that performs the job. Accepts connection instance as parameter.
     * @param string|null $isolationLevel The isolation level to use for this transaction.
     * See [[Transaction::begin()]] for details.
     *
     * @throws \Exception|\Throwable if there is any exception during query. In this case the transaction will be
     *     rolled back.
     * @return mixed result of callback function
     */
    public static function transaction(callable $callback, $isolationLevel = null)
    {
        self::throwException();
        $result = Yii::$app->getDb()->transaction($callback, $isolationLevel);
        self::throwException(false);

        return $result;
    }

    /**
     * 抛出异常
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
     * 抛出异常
     *
     * @param boolean $throw
     */
    public static function throwException($throw = true)
    {
        static::$_throwException = $throw;
    }

}
