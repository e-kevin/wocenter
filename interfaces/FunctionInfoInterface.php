<?php

namespace wocenter\interfaces;

/**
 * 功能扩展详情接口类
 *
 * @property string $moduleId 扩展所属模块ID
 * @property string $migrationPath 数据库迁移路径
 * @property array $menus 扩展菜单信息，只读属性
 * @property array $urlRule 扩展路由规则，只读属性
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface FunctionInfoInterface
{
    
    /**
     * 获取扩展所属模块ID
     *
     * @return string
     */
    public function getModuleId();
    
    /**
     * 设置扩展所属模块ID
     *
     * @param string $moduleId 模块ID
     */
    public function setModuleId($moduleId);
    
    /**
     * 获取数据库迁移路径
     *
     * @return string
     */
    public function getMigrationPath();
    
    /**
     * 设置数据库迁移路径
     *
     * @param string $migrationPath 数据库迁移路径
     */
    public function setMigrationPath($migrationPath);
    
    /**
     * 获取扩展菜单信息
     *
     * @return array
     */
    public function getMenus();
    
    /**
     * 获取扩展路由规则
     *
     * @return array
     */
    public function getUrlRules();
    
}
