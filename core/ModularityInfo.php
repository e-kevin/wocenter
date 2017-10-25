<?php

namespace wocenter\core;

use wocenter\interfaces\ModularityInfoInterface;

/**
 * 基础模块信息实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ModularityInfo extends Extension implements ModularityInfoInterface
{
    
    /**
     * @var boolean 是否启用bootstrap
     */
    public $bootstrap = false;
    
    /**
     * @var string 数据库迁移路径
     */
    private $_migrationPath;
    
    /**
     * 模块菜单信息
     *
     * 系统模块菜单是一个多维数组。系统默认已经在System模块菜单里添加一些主要的顶级菜单数据，其他模块归属已经存在的顶级菜单时只需添加以
     * `name`为键名，顶级菜单为键值的键值对即可，如Account模块菜单，归属系统`人事管理`顶级菜单，故添加代码`'name' => '人事管理'`
     * 即可表示该菜单归属`人事管理`。而子菜单只需添加在键名为`items`的数组里，参见Account模块的菜单配置数据。
     *
     * 菜单配置数组里，可用的键名包含以下：
     *  - `name`: 菜单名称，建议简写，如`模块列表`简写为`列表`，在某些地方显示时可以简洁的文字显示出来，如系统的权限配置里所显示的
     *  - `alias_name`: 菜单别名，用于显示在菜单列表里的稍微详细的名字
     *  - `icon_html`: 菜单图标
     *  - `modularity`: 菜单所属的模块，一般为当前模块，不填写系统则会自动从`full_url`里提取出菜单所属的模块。如果`full_url`为空
     *      或不是规则的路由地址，如系统默认为父级菜单赋值的`javascript:;`情况时，必须明确该菜单所属模块，否则系统不会同步更新到菜单
     *      数据库里。
     *  - `url`: 简写的url地址，可以是系统已经配置了的路由映射地址，默认为`javascript:;`
     *  - `show_on_menu`: 是否显示该菜单，默认不显示
     *  - `description`: 菜单描述
     *  - `items`: 子类菜单配置数组
     *
     * @see \wocenter\backend\modules\account\Info::getMenus()
     * @return array
     */
    public function getMenus()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function getUrlRules()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function getMigrationPath()
    {
        return $this->_migrationPath;
    }
    
    /**
     * @inheritdoc
     */
    public function setMigrationPath($migrationPath)
    {
        $this->_migrationPath = $migrationPath;
    }
}
