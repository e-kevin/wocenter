<?php

namespace wocenter\helpers;

use Yii;
use yii\{
    caching\ApcCache, caching\CacheInterface
};

/**
 * 缓存管理助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class CacheManagerHelper
{
    
    /**
     * Flushes all caches registered in the system.
     */
    public static function flushAll()
    {
        $cachesInfo = [];
        if (empty($caches = self::findCaches())) {
            return $cachesInfo;
        }
        
        foreach ($caches as $name => $class) {
            $cachesInfo[] = [
                'name' => $name,
                'class' => $class,
                'is_flushed' => self::_canBeFlushed($class) ? Yii::$app->get($name)->flush() : false,
            ];
        }
        
        return $cachesInfo;
    }
    
    /**
     * Returns array of caches in the system, keys are cache components names, values are class names.
     *
     * @param array $cachesNames caches to be found
     *
     * @return array
     */
    private static function findCaches(array $cachesNames = [])
    {
        $caches = [];
        $components = Yii::$app->getComponents();
        $findAll = ($cachesNames === []);
        
        foreach ($components as $name => $component) {
            if (!$findAll && !in_array($name, $cachesNames, true)) {
                continue;
            }
            
            if ($component instanceof CacheInterface) {
                $caches[$name] = get_class($component);
            } elseif (is_array($component) && isset($component['class']) && self::_isCacheClass($component['class'])) {
                $caches[$name] = $component['class'];
            } elseif (is_string($component) && self::_isCacheClass($component)) {
                $caches[$name] = $component;
            } elseif ($component instanceof \Closure) {
                $cache = Yii::$app->get($name);
                if (self::_isCacheClass($cache)) {
                    $caches[$name] = get_class($cache);
                }
            }
        }
        
        return $caches;
    }
    
    /**
     * Checks if given class is a Cache class.
     *
     * @param string $className class name.
     *
     * @return bool
     */
    private static function _isCacheClass($className)
    {
        return is_subclass_of($className, 'yii\caching\CacheInterface');
    }
    
    /**
     * Checks if cache of a certain class can be flushed.
     *
     * @param string $className class name.
     *
     * @return bool
     */
    private static function _canBeFlushed($className)
    {
        return !is_a($className, ApcCache::class, true) || php_sapi_name() !== 'cli';
    }
    
}
