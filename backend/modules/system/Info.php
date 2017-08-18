<?php
namespace wocenter\backend\modules\system;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->id = 'system';
        $this->name = '系统管理';
        $this->version = '1.0';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '提供网站设置、配置管理等';
        $this->isSystem = true;
        $this->defaultRoute = 'config-manager';
    }

    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            // 扩展中心
            [
                'name' => '扩展中心',
                'icon_html' => 'cube',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1005,
            ],
            // 人事管理
            [
                'name' => '人事管理',
                'icon_html' => 'user',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1001,
            ],
            // 安全管理
            [
                'name' => '安全管理',
                'icon_html' => 'shield',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1003,
            ],
            // 运营管理
            [
                'name' => '运营管理',
                'icon_html' => 'line-chart',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1004,
            ],
            [
                'name' => '系统管理',
                'icon_html' => 'cog',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1002,
                'items' => [
                    // 网站设置
                    [
                        'name' => '网站设置',
                        'icon_html' => 'sliders',
                        'modularity' => $this->id,
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '基础配置', 'url' => "/{$this->id}/setting/basic", 'show_on_menu' => true],
                            ['name' => '内容配置', 'url' => "/{$this->id}/setting/content", 'show_on_menu' => true],
                            ['name' => '注册配置', 'url' => "/{$this->id}/setting/register", 'show_on_menu' => true],
                            ['name' => '系统配置', 'url' => "/{$this->id}/setting/config", 'show_on_menu' => true],
                            ['name' => '安全配置', 'url' => "/{$this->id}/setting/security", 'show_on_menu' => true],
                        ],
                    ],
                    [
                        'name' => '基础功能',
                        'icon_html' => 'cogs',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            // 配置管理
                            [
                                'name' => '配置管理',
                                'url' => "/{$this->id}",
                                'full_url' => "/{$this->id}/{$this->defaultRoute}/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/{$this->defaultRoute}/index", 'description' => '配置管理列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/{$this->defaultRoute}/create"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/{$this->defaultRoute}/update"],
                                    ['name' => '删除', 'url' => "/{$this->id}/{$this->defaultRoute}/delete"],
                                    ['name' => '搜索', 'url' => "/{$this->id}/{$this->defaultRoute}/search"],
                                ],
                            ],
                            ['name' => '清理缓存', 'url' => "/{$this->id}/cache/flushCache", 'show_on_menu' => true],
                        ],
                    ],
                ],
            ],
        ];
    }

}
