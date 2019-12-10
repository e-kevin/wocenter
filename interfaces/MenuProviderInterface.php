<?php

namespace wocenter\interfaces;

use wocenter\db\ActiveRecord;
use yii\base\InvalidConfigException;

/**
 * 菜单配置提供者接口类
 *
 * @property ActiveRecord $model
 * @property array $menuConfig
 * @property array $all
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface MenuProviderInterface
{
    
    /**
     * 菜单创建者为用户
     */
    const CREATE_TYPE_BY_USER = 0;
    
    /**
     * 菜单创建者为扩展
     */
    const CREATE_TYPE_BY_EXTENSION = 1;
    
    /**
     * 获取所有菜单数据
     *
     * @param null|int $level 菜单级别
     *
     * @return array
     * - 当$level不为`null`时，则获取指定菜单级别的菜单数据，并返回以$level值为索引的菜单数据分组
     * - 默认返回以主键值为索引的菜单数据分组
     */
    public function getAll($level = null): array;
    
    /**
     * 删除缓存
     */
    public function clearCache();
    
    /**
     * 获取菜单模型类
     *
     * @return ActiveRecord
     */
    public function getModel();
    
    /**
     * 设置菜单模型类
     *
     * @param array $config
     *
     * @throws InvalidConfigException
     */
    public function setModel($config = []);
    
    /**
     * 获取菜单配置数据
     *
     * @return array
     * [
     *  {app} => [],
     * ]
     */
    public function getMenuConfig();
    
    /**
     * 设置菜单配置数据
     *
     * @param array $config 菜单配置数据，该数据为未写入数据库的菜单数组数据，
     * 一般为系统默认的菜单数据或扩展配置文件里的菜单数据
     * @see ModularityInfoInterface::getMenus()
     * @see ControllerInfoInterface::getMenus()
     *
     * [
     *  {app} => [],
     * ]
     */
    public function setMenuConfig($config = []);
    
}
