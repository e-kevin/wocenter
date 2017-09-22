<?php
namespace wocenter\backend\modules\account;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    /**
     * @inheritdoc
     */
    public $name = '人事管理';

    /**
     * @inheritdoc
     */
    public $description = '提供用户、身份、档案、标签等功能的管理';

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
            // 人事管理
            [
                'name' => '人事管理',
                'icon_html' => 'user',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1001,
                'items' => [
                    // 用户管理
                    [
                        'name' => '用户管理',
                        'icon_html' => 'users',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '用户列表', 'url' => "/{$this->getId()}/user/index",
                                'description' => '用户列表', 'show_on_menu' => true,
                            ],
                            ['name' => '禁用列表', 'url' => "/{$this->getId()}/user/forbidden-list",
                                'description' => '禁用列表', 'show_on_menu' => true,
                            ],
                            ['name' => '锁定列表', 'url' => "/{$this->getId()}/user/locked-list",
                                'description' => '锁定列表', 'show_on_menu' => true,
                            ],
                            [
                                'name' => '管理员列表',
                                'url' => "/{$this->getId()}/admin/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/admin/index", 'description' => '管理员列表'],
                                    ['name' => '添加', 'url' => "/{$this->getId()}/admin/add", 'description' => '添加管理员'],
                                    ['name' => '解除', 'url' => "/{$this->getId()}/admin/relieve", 'description' => '解除管理员'],
                                ],
                            ],
                            // 用户行为
                            [
                                'name' => '用户行为',
                                'modularity' => $this->getId(),
                                'items' => [
                                    ['name' => '生成用户', 'url' => "/{$this->getId()}/user/generate", 'description' => '快速生成用户'],
                                    ['name' => '禁用', 'url' => "/{$this->getId()}/user/forbidden", 'description' => '禁用用户'],
                                    ['name' => '激活', 'url' => "/{$this->getId()}/user/active", 'description' => '激活用户'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/user/delete", 'description' => '删除用户'],
                                    ['name' => '详情', 'url' => "/{$this->getId()}/user/view", 'description' => '查看用户详情'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/user/update", 'description' => '更新用户'],
                                    ['name' => '重置', 'url' => "/{$this->getId()}/user/init-password", 'description' => '重置用户密码'],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/user/search", 'description' => '搜索用户'],
                                ],
                            ],
                        ],
                    ],
                    // 身份管理
                    [
                        'name' => '身份管理',
                        'icon_html' => 'vcard',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            // 身份列表
                            [
                                'name' => '身份列表',
                                'url' => "/{$this->getId()}/identity/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/identity/index", 'description' => '身份列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/identity/create", 'description' => '新增身份'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/identity/update", 'description' => '编辑身份'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/identity/delete", 'description' => '删除身份'],
                                    ['name' => '配置', 'url' => "/{$this->getId()}/identity/setting", 'description' => '身份默认信息配置'],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/identity/search", 'description' => '搜索身份'],
                                ],
                            ],
                            // 身份分组列表
                            [
                                'name' => '身份分组列表',
                                'url' => "/{$this->getId()}/identity-group/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/identity-group/index", 'description' => '身份分组列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/identity-group/create", 'description' => '新增身份分组'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/identity-group/update", 'description' => '编辑身份分组'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/identity-group/delete", 'description' => '删除身份分组'],
                                ],
                            ],
                            // 用户身份管理
//                            [
//                                'name' => '用户身份管理',
//                                'user' => "/{$this->getId()}/identity-user/index",
//                                'show_on_menu' => true,
//                                'items' => [
//                                    ['name' => '列表', 'url' => "/{$this->id}/identity-user/index"],
//                                    ['name' => '新增', 'url' => "/{$this->id}/identity-user/create"],
//                                    ['name' => '编辑', 'url' => "/{$this->id}/identity-user/update"],
//                                    ['name' => '删除', 'url' => "/{$this->id}/identity-user/delete"],
//                                ],
//                            ],
                        ],
                    ],
                    // 档案管理
                    [
                        'name' => '档案管理',
                        'icon_html' => 'user-circle-o',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            // 扩展档案列表
                            [
                                'name' => '档案列表',
                                'url' => "/{$this->getId()}/profile/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/profile/index", 'description' => '扩展档案列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/profile/create", 'description' => '新增扩展档案'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/profile/update", 'description' => '编辑扩展档案'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/profile/delete", 'description' => '删除扩展档案'],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/profile/search", 'description' => '搜索档案'],
                                ],
                            ],
                            // 字段管理
                            [
                                'name' => '字段管理',
                                'modularity' => $this->getId(),
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/field/index", 'description' => '扩展档案字段列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/field/create", 'description' => '新增扩展档案字段'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/field/update", 'description' => '编辑扩展档案字段'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/field/delete", 'description' => '删除扩展档案字段'],
                                ],
                            ],
                        ],
                    ],
                    // 标签管理
                    [
                        'name' => '标签管理',
                        'icon_html' => 'tags',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            // 标签列表
                            [
                                'name' => '标签列表',
                                'icon_html' => 'tags',
                                'url' => "/{$this->getId()}/tag/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/tag/index", 'description' => '标签列表'],
                                    ['name' => '新增', 'url' => "/{$this->getId()}/tag/create", 'description' => '新增标签'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/tag/update", 'description' => '编辑标签'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/tag/delete", 'description' => '删除标签'],
                                    ['name' => '批量删除', 'url' => "/{$this->getId()}/tag/batch-delete", 'description' => '批量删除标签'],
                                ],
                            ],
                        ]
                    ],
                ],
            ],
        ];
    }
    
}
