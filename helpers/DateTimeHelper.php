<?php

namespace wocenter\helpers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FormatConverter;

/**
 * 日期时间助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DateTimeHelper
{
    
    const HOUR = 0;
    const MINUTE = 1;
    const SECOND = 2;
    const YEAR = 3;
    const MONTH = 4;
    const DAY = 5;
    const WEEK = 6;
    
    /**
     * 获取时间单位列表
     *
     * @return array
     */
    public static function getTimeUnitList()
    {
        return [
            self::HOUR => Yii::t('wocenter/app', 'Hour'),
            self::MINUTE => Yii::t('wocenter/app', 'Minute'),
            self::SECOND => Yii::t('wocenter/app', 'Second'),
            self::YEAR => Yii::t('wocenter/app', 'Year'),
            self::MONTH => Yii::t('wocenter/app', 'Month'),
            self::DAY => Yii::t('wocenter/app', 'Day'),
            self::WEEK => Yii::t('wocenter/app', 'Week'),
        ];
    }
    
    /**
     * 获取时间单位值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getTimeUnitValue($key = null)
    {
        return ArrayHelper::getValue(static::getTimeUnitList(), $key);
    }
    
    /**
     * 标准化格式时间戳
     *
     * @param integer $timestamp 需要格式化的日期时间
     * @param string $format 时间格式，默认为 Y-m-d H:i:s
     *
     * @return string|null 完整的时间显示
     */
    public static function timeFormat($timestamp = null, $format = 'Y-m-d H:i:s')
    {
        if ($timestamp === null) {
            $timestamp = time();
        } elseif (empty($timestamp)) {
            return null;
        } else {
            $timestamp = intval($timestamp);
        }
        if (strncmp($format, 'php:', 4) === 0) {
            $format = substr($format, 4);
        }
        
        return date($format, $timestamp);
    }
    
    /**
     * 格式化为友好时间格式
     *
     * @param integer $timestamp 时间戳
     *
     * @return string 友好时间格式
     */
    public static function timeFriendly($timestamp = null)
    {
        if ($timestamp == null) {
            return 'N/A';
        }
        
        // 获取当前时间戳
        $currentTime = time();
        
        // 获取当天0点时间戳
        $todayZero = strtotime('today');
        
        // 获取昨天时间戳
        $yesterday = strtotime('-1 day', $todayZero);
        
        // 获取前天时间戳
        $beforeYesterday = strtotime('-1 day', $yesterday);
        
        // 获取明天0点时间戳
        $tomorrow = strtotime('+1 day', $todayZero);
        
        // 获取后天0点时间戳
        $afterTomorrow = strtotime('+1 day', $tomorrow);
        
        // 获取一天的时间戳
        $oneDayTimestamp = 3600 * 24;
        
        //当年时间戳
        $yearDiff = $currentTime - strtotime("-1 year");
        
        // 时间差
        $timeDiff = $currentTime - $timestamp;
        
        switch (true) {
            case $timestamp >= $afterTomorrow && $timestamp < $afterTomorrow + $oneDayTimestamp :
                return Yii::t('wocenter/app', 'The day after tomorrow {time}', ['time' => date('H:i', $timestamp)]);
            case $timestamp >= $tomorrow && $timestamp < $afterTomorrow :
                return Yii::t('wocenter/app', 'Tomorrow {time}', ['time' => date('H:i', $timestamp)]);
            case $timestamp >= $todayZero && $timestamp < $tomorrow :
                if ($timeDiff < (3600 * 8)) {
                    return Yii::$app->getFormatter()->asRelativeTime($timestamp);
                } else {
                    return Yii::t('wocenter/app', 'Today {time}', ['time' => date('H:i', $timestamp)]);
                }
            case $timestamp >= $yesterday && $timestamp < $todayZero :
                return Yii::t('wocenter/app', 'Yesterday {time}', ['time' => date('H:i', $timestamp)]);
            case $timestamp >= $beforeYesterday && $timestamp < $yesterday :
                return Yii::t('wocenter/app', 'The day before yesterday {time}', ['time' => date('H:i', $timestamp)]);
            default :
                if ($timeDiff > $yearDiff) {
                    return date('Y-m-d H:i', $timestamp);
                } else {
                    return date('m-d H:i', $timestamp);
                }
        }
    }
    
    /**
     * 根据时间单位，比较两个时间差。即$timestamp距离$time(默认为当前时间)已过多久
     *
     * @param integer $timestamp 需要比较的时间
     * @param int|string $type 需要比较的时间单位，默认为秒， [时，分，秒，年，月，日，周];
     * @param null|integer $time 被比较的时间，默认为当前时间
     *
     * @return int 两个时间差
     */
    public static function getTimeAgo($timestamp = 1, $type = self::SECOND, $time = null)
    {
        if (empty($time)) {
            $time = time();
        }
        switch ($type) {
            case self::HOUR:
                $result = $time - $timestamp * 60 * 60;
                break;
            case self::MINUTE:
                $result = $time - $timestamp * 60;
                break;
            case self::SECOND:
                $result = $time - $timestamp;
                break;
            case self::YEAR:
                $result = strtotime('-' . $timestamp . ' year', $time);
                break;
            case self::MONTH:
                $result = strtotime('-' . $timestamp . ' month', $time);
                break;
            case self::DAY:
                $result = strtotime('-' . $timestamp . ' day', $time);
                break;
            case self::WEEK:
                $result = strtotime('-' . ($timestamp * 7) . ' day', $time);
                break;
            default:
                $result = $time - $timestamp;
        }
        
        return $result;
    }
    
    /**
     * 根据时间单位，获取一个给定时间的未来时间戳。即$timestamp后$time的时间戳
     *
     * @param integer $timestamp 需要比较的时间
     * @param int|string $type 需要比较的时间单位 [时，分，秒，年，月，日，周];
     * @param null|integer $time 已该时间戳为起点
     * - null，则为time()
     *
     * @return int
     */
    public static function getAfterTime($timestamp = 1, $type = self::SECOND, $time = null)
    {
        if (empty($time)) {
            $time = time();
        }
        switch ($type) {
            case self::MINUTE:
                $result = $time + $timestamp * 60;
                break;
            case self::HOUR:
                $result = $time + $timestamp * 60 * 60;
                break;
            case self::SECOND:
                $result = $time + $timestamp;
                break;
            case self::YEAR:
                $result = strtotime('+' . $timestamp . ' year', $time);
                break;
            case self::MONTH:
                $result = strtotime('+' . $timestamp . ' month', $time);
                break;
            case self::DAY:
                $result = strtotime('+' . $timestamp . ' day', $time);
                break;
            case self::WEEK:
                $result = strtotime('+' . ($timestamp * 7) . ' day', $time);
                break;
            default:
                $result = $time + $timestamp;
        }
        
        return $result;
    }
    
    /**
     * 解析并返回PHP DateTime能够理解的时间日期格式
     *
     * @param string $format ICU或php类型的时间日期格式
     * 配置示例：
     * ```php
     * 'MM/dd/yyyy' // ICU格式
     * 'php:m/d/Y' // PHP格式
     * ```
     * @param string $type 时间日期类型，可选值有： `date`, `time`, `datetime`
     *
     * @return string
     * @throws InvalidConfigException
     */
    public static function parseFormat($format, $type)
    {
        if (strncmp($format, 'php:', 4) === 0) {
            return substr($format, 4);
        } elseif ($format != '') {
            return FormatConverter::convertDateIcuToPhp($format, $type);
        } else {
            throw new InvalidConfigException("Error parsing '{$type}' format.");
        }
    }
    
}
