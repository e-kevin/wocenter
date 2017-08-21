<?php
namespace wocenter\backend\modules\menu;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->name = '菜单管理';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '提供所有的菜单功能支持';
        $this->isSystem = true;
    }

    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            [
                'name' => '系统管理',
                'items' => [
                    // 基础功能
                    [
                        'name' => '基础功能',
                        'icon_html' => 'cogs',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            // 菜单管理
                            [
                                'name' => '菜单管理',
                                'url' => "/{$this->getId()}/category/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/category/index", 'description' => '菜单分类列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/category/create", 'description' => '新增菜单分类'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/category/update", 'description' => '编辑菜单分类'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/category/delete", 'description' => '删除菜单分类'],
                                    ['name' => '同步', 'url' => "/{$this->getId()}/category/sync-menus", 'description' => '同步后台菜单'],
                                    [
                                        'name' => '管理',
                                        'url' => "/{$this->getId()}/detail/index",
                                        'description' => '菜单明细管理',
                                        'items' => [
                                            ['name' => '列表', 'url' => "/{$this->getId()}/detail/index", 'description' => '菜单列表'],
                                            ['name' => '新增', 'url' => "/{$this->getId()}/detail/create", 'description' => '新增菜单'],
                                            ['name' => '编辑', 'url' => "/{$this->getId()}/detail/update", 'description' => '编辑菜单'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
