<?php

namespace wocenter\libs;

use wocenter\helpers\ArrayHelper;
use wocenter\interfaces\IdentityInterface;
use Yii;

/**
 * 系统常量
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Constants
{
    
    /**
     * @var integer 不限
     */
    const UNLIMITED = 999;
    
    /**
     * @var integer 链接打开方式：当前窗口
     */
    const TARGET_SELF = 0;
    
    /**
     * @var integer 链接打开方式：新建窗口
     */
    const TARGET_BLANK = 1;
    
    /**
     * 获取`是`，`否`列表
     *
     * @return array
     */
    public static function getYesOrNoList()
    {
        return [
            Yii::t('wocenter/app', 'No'),
            Yii::t('wocenter/app', 'Yes'),
        ];
    }
    
    /**
     * 获取`是`，`否`值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getYesOrNoValue($key = null)
    {
        return ArrayHelper::getValue(static::getYesOrNoList(), $key);
    }
    
    /**
     * 获取状态列表
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            Yii::t('wocenter/app', 'Disable'),
            Yii::t('wocenter/app', 'Enable'),
        ];
    }
    
    /**
     * 获取状态值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getStatusValue($key = null)
    {
        return ArrayHelper::getValue(static::getStatusList(), $key);
    }
    
    /**
     * 获取窗口打开列表
     *
     * @return array
     */
    public static function getOpenTargetList()
    {
        return [
            self::TARGET_SELF => Yii::t('wocenter/app', 'Target self'),
            self::TARGET_BLANK => Yii::t('wocenter/app', 'Target blank'),
        ];
    }
    
    /**
     * 获取窗口打开值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getOpenTargetValue($key = null)
    {
        return ArrayHelper::getValue(static::getOpenTargetList(), $key);
    }
    
    /**
     * 获取性别列表
     *
     * @return array
     */
    public static function getGenderList()
    {
        return [
            Yii::t('wocenter/app', 'Secrecy'),
            Yii::t('wocenter/app', 'Male'),
            Yii::t('wocenter/app', 'Female'),
        ];
    }
    
    /**
     * 获取性别值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getGenderValue($key = null)
    {
        return ArrayHelper::getValue(static::getGenderList(), $key);
    }
    
    /**
     * 获取可见性列表
     *
     * @return array
     */
    public static function getVisibleList()
    {
        return [
            Yii::t('wocenter/app', 'Hidden'),
            Yii::t('wocenter/app', 'Display'),
        ];
    }
    
    /**
     * 获取可见值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getVisibleValue($key = null)
    {
        return ArrayHelper::getValue(static::getVisibleList(), $key);
    }
    
    /**
     * 获取用户状态列表
     *
     * @return array
     */
    public static function getUserStatusList()
    {
        return [
            IdentityInterface::STATUS_FORBIDDEN => Yii::t('wocenter/app', 'User disabled status'),
            IdentityInterface::STATUS_ACTIVE => Yii::t('wocenter/app', 'User active status'),
            IdentityInterface::STATUS_LOCKED => Yii::t('wocenter/app', 'User locked status'),
            IdentityInterface::STATUS_DELETED => Yii::t('wocenter/app', 'User deleted status'),
        ];
    }
    
    /**
     * 获取用户状态值
     *
     * @param string|integer $key
     *
     * @return mixed
     */
    public static function getUserStatusValue($key = null)
    {
        return ArrayHelper::getValue(self::getUserStatusList(), $key);
    }
    
}
