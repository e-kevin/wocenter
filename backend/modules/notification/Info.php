<?php
namespace wocenter\backend\modules\notification;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    /**
     * @inheritdoc
     */
    public $name = '系统通知管理';

    /**
     * @inheritdoc
     */
    public $description = '系统通知管理模块，如：邮件通知、站内信、公告等';

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
                    // 基础功能
                    [
                        'name' => '基础功能',
                        'icon_html' => 'cogs',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            // 通知管理
                            [
                                'name' => '通知管理',
                                'url' => "/{$this->getId()}/setting/index",
                                'show_on_menu' => true,
                                'sort_order' => 20,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/setting/index", 'description' => '通知管理列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/setting/create", 'description' => '新增通知'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/setting/update", 'description' => '编辑通知'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/setting/delete", 'description' => '删除通知'],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/setting/search", 'description' => '搜索通知'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
