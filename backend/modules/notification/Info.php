<?php
namespace wocenter\backend\modules\notification;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->id = 'notification';
        $this->name = '系统通知管理';
        $this->version = '1.0';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '系统通知管理模块，如：邮件通知、站内信、公告等';
        $this->isSystem = true;
        $this->defaultRoute = 'setting';
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
                            // 通知管理
                            [
                                'name' => '通知管理',
                                'url' => "/{$this->id}",
                                'full_url' => "/{$this->id}/{$this->defaultRoute}/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/{$this->defaultRoute}/index", 'description' => '通知管理列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/{$this->defaultRoute}/create", 'description' => '新增通知'],
                                    ['name' => '编辑', 'url' => "/{$this->id}/{$this->defaultRoute}/update", 'description' => '编辑通知'],
                                    ['name' => '删除', 'url' => "/{$this->id}/{$this->defaultRoute}/delete", 'description' => '删除通知'],
                                    ['name' => '搜索', 'url' => "/{$this->id}/{$this->defaultRoute}/search", 'description' => '搜索通知'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
