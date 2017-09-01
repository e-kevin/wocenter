<?php
namespace wocenter\backend\modules\system;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    /**
     * @inheritdoc
     */
    public $name = '系统管理';

    /**
     * @inheritdoc
     */
    public $description = '提供网站设置、配置管理等';

    /**
     * @inheritdoc
     */
    public $isSystem = true;

    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            // 系统管理
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
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '基础配置', 'url' => "/{$this->getId()}/setting/basic", 'show_on_menu' => true],
                            ['name' => '内容配置', 'url' => "/{$this->getId()}/setting/content", 'show_on_menu' => true],
                            ['name' => '注册配置', 'url' => "/{$this->getId()}/setting/register", 'show_on_menu' => true],
                            ['name' => '系统配置', 'url' => "/{$this->getId()}/setting/config", 'show_on_menu' => true],
                            ['name' => '安全配置', 'url' => "/{$this->getId()}/setting/security", 'show_on_menu' => true],
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
                                'url' => "/{$this->getId()}/config-manager/index",
                                'show_on_menu' => true,
                                'sort_order' => 30,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/config-manager/index", 'description' => '配置管理列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/config-manager/create"],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/config-manager/update"],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/config-manager/delete"],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/config-manager/search"],
                                ],
                            ],
                            ['name' => '清理缓存', 'url' => "/{$this->getId()}/cache/flushCache", 'show_on_menu' => true, 'sort_order' => 100],
                        ],
                    ],
                ],
            ],
        ];
    }

}
