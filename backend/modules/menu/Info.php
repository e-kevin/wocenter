<?php
namespace wocenter\backend\modules\menu;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->id = 'menu';
        $this->name = '菜单管理';
        $this->version = '1.0';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '提供所有的菜单功能支持';
        $this->isSystem = true;
        $this->defaultRoute = 'category';
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
                                'url' => "/{$this->id}",
                                'full_url' => "/{$this->id}/{$this->defaultRoute}/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/{$this->defaultRoute}/index", 'description' => '菜单分类列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/{$this->defaultRoute}/create", 'description' => '新增菜单分类'],
                                    ['name' => '编辑', 'url' => "/{$this->id}/{$this->defaultRoute}/update", 'description' => '编辑菜单分类'],
                                    ['name' => '删除', 'url' => "/{$this->id}/{$this->defaultRoute}/delete", 'description' => '删除菜单分类'],
                                    ['name' => '同步', 'url' => "/{$this->id}/{$this->defaultRoute}/sync-menus", 'description' => '同步后台菜单'],
                                    ['name' => '管理', 'url' => "/{$this->id}/detail/index", 'description' => '菜单明细管理'],
                                ],
                            ],
                        ]
                    ],
                ]
            ],
        ];
    }

}
