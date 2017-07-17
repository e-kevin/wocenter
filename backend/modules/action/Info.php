<?php
namespace wocenter\backend\modules\action;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();
        
        $this->id = 'action';
        $this->name = '行为管理';
        $this->description = '管理系统所有行为操作';
        $this->version = '1.0';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->isSystem = true;
        $this->defaultRoute = 'manage';
    }

    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            // 安全管理
            [
                'name' => '安全管理',
                'items' => [
                    [
                        'name' => '行为管理',
                        'icon_html' => 'paw',
                        'url' => "/{$this->id}",
                        'full_url' => "/{$this->id}/{$this->defaultRoute}/index",
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '列表', 'url' => "/{$this->id}/{$this->defaultRoute}/index", 'description' => '行为列表',],
                            ['name' => '新增', 'url' => "/{$this->id}/{$this->defaultRoute}/create"],
                            ['name' => '编辑', 'url' => "/{$this->id}/{$this->defaultRoute}/update"],
                            ['name' => '删除', 'url' => "/{$this->id}/{$this->defaultRoute}/delete"],
                            ['name' => '搜索', 'url' => "/{$this->id}/{$this->defaultRoute}/search", 'description' => '搜索行为'],
                        ]
                    ],
                    [
                        'name' => '行为限制管理',
                        'icon_html' => 'paw',
                        'url' => "/{$this->id}/limit",
                        'full_url' => "/{$this->id}/limit/index",
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '列表', 'url' => "/{$this->id}/limit/index", 'description' => '行为限制列表'],
                            ['name' => '新增', 'url' => "/{$this->id}/limit/create"],
                            ['name' => '编辑', 'url' => "/{$this->id}/limit/update"],
                            ['name' => '删除', 'url' => "/{$this->id}/limit/delete"],
                            ['name' => '搜索', 'url' => "/{$this->id}/limit/search", 'description' => '搜索行为限制'],
                        ]
                    ],
                ]
            ],
        ];
    }
    
}
