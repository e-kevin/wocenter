<?php

namespace wocenter\interfaces;

use wocenter\core\MenuProvider;

/**
 * 模块详情接口类
 *
 * @property array $menus 模块菜单信息，只读属性
 * @property string $migrationPath 数据库迁移路径
 * @property array $config 模块扩展配置信息
 * @property array $configKey 模块扩展配置信息允许的键名
 * @property array $behaviors 行为配置信息
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ModularityInfoInterface
{
    
    /**
     * 获取模块菜单信息
     *
     * @see MenuProvider::defaultMenuConfig()
     *
     * @return array
     */
    public function getMenus();
    
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
