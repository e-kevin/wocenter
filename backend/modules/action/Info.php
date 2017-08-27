<?php
namespace wocenter\backend\modules\action;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->name = '行为管理';
        $this->description = '管理系统所有行为操作，如：行为管理、行为限制管理';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->isSystem = true;
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
                'icon_html' => 'shield',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1003,
                'items' => [
                    [
                        'name' => '行为管理',
                        'icon_html' => 'paw',
                        'url' => "/{$this->getId()}/manage/index",
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '列表', 'url' => "/{$this->getId()}/manage/index", 'description' => '行为列表',],
                            ['name' => '新增', 'url' => "/{$this->getId()}/manage/create"],
                            ['name' => '编辑', 'url' => "/{$this->getId()}/manage/update"],
                            ['name' => '删除', 'url' => "/{$this->getId()}/manage/delete"],
                            ['name' => '搜索', 'url' => "/{$this->getId()}/manage/search", 'description' => '搜索行为'],
                        ],
                    ],
                    [
                        'name' => '行为限制管理',
                        'icon_html' => 'paw',
                        'url' => "/{$this->getId()}/limit/index",
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '列表', 'url' => "/{$this->getId()}/limit/index", 'description' => '行为限制列表'],
                            ['name' => '新增', 'url' => "/{$this->getId()}/limit/create"],
                            ['name' => '编辑', 'url' => "/{$this->getId()}/limit/update"],
                            ['name' => '删除', 'url' => "/{$this->getId()}/limit/delete"],
                            ['name' => '搜索', 'url' => "/{$this->getId()}/limit/search", 'description' => '搜索行为限制'],
                        ],
                    ],
                ],
            ],
        ];
    }

}
