<?php

namespace wocenter\interfaces;

/**
 * 模块详情接口类
 *
 * @property array $menus 模块菜单信息，只读属性
 * @property array $urlRule 模块路由规则，只读属性
 * @property string $migrationPath 数据库迁移路径
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ModularityInfoInterface
{
    
    /**
     * 获取模块菜单信息
     *
     * @return array
     */
    public function getMenus();
    
    /**
     * 获取模块路由规则
     *
     * @return array
     */
    public function getUrlRules();
    
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
    
}
