<?php
namespace wocenter\backend\modules\operate;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    /**
     * @inheritdoc
     */
    public $name = '运营管理';

    /**
     * @inheritdoc
     */
    public $description = '管理系统所有运营数据，邀请码、头衔等';

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
            // 运营管理
            [
                'name' => '运营管理',
                'icon_html' => 'line-chart',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1004,
                // 邀请码管理
                'items' => [
                    [
                        'name' => '邀请管理',
                        'icon_html' => 'key',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'sort_order' => 10,
                        'items' => [
                            // 类型列表
                            [
                                'name' => '邀请码类型',
                                'url' => "/{$this->getId()}/invite-type/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/invite-type/index"],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/invite-type/create"],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/invite-type/update"],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/invite-type/delete"],
                                ],
                            ],
                            [
                                'name' => '邀请码列表',
                                'url' => "/{$this->getId()}/invite/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/invite/index"],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/invite/search"],
                                    ['name' => '生成', 'url' => "/{$this->getId()}/invite/generate"],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/invite/delete"],
                                    ['name' => '批量删除', 'url' => "/{$this->getId()}/invite/batch-delete"],
                                    ['name' => '清空', 'url' => "/{$this->getId()}/invite/clear", 'description' => '清空无用邀请码（真删除）'],
                                ],
                            ],
                            [
                                'name' => '邀请记录',
                                'url' => "/{$this->getId()}/invite-log/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/invite-log/index"],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/invite-log/search"],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/invite-log/delete"],
                                    ['name' => '批量删除', 'url' => "/{$this->getId()}/invite-log/batch-delete"],
                                ],
                            ],
//                            ['name' => '兑换记录', 'url' => "/{$this->id}/invite-buy-log", 'show_on_menu' => true],
//                            ['name' => '邀请人列表', 'url' => "/{$this->id}/invite-user-info", 'show_on_menu' => true],
                        ],
                    ],
                    // 头衔管理
                    [
                        'name' => '头衔管理',
                        'icon_html' => 'mortar-board',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'sort_order' => 20,
                        'items' => [
                            [
                                'name' => '头衔列表',
                                'url' => "/{$this->getId()}/rank/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/rank/index", 'description' => '头衔列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/rank/create"],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/rank/update"],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/rank/delete"],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
