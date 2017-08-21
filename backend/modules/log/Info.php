<?php
namespace wocenter\backend\modules\log;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->name = '日志管理';
        $this->description = '管理系统所有日志，如：行为日志、积分日志';
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
                'items' => [
                    [
                        'name' => '日志管理',
                        'icon_html' => 'list',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            // 行为日志
                            [
                                'name' => '行为日志',
                                'url' => "/{$this->getId()}/action/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/action/index", 'description' => '行为日志列表'],
                                    ['name' => '删除', 'url' => "/{$this->getId()}/action/delete"],
                                    ['name' => '批量删除', 'url' => "/{$this->getId()}/action/batch-delete"],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/action/search", 'description' => '搜索行为日志'],
                                ],
                            ],
                            // 奖罚日志
                            [
                                'name' => '奖罚日志',
                                'url' => "/{$this->getId()}/score/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/score/index", 'description' => '奖罚日志列表'],
                                    ['name' => '搜索', 'url' => "/{$this->getId()}/score/search", 'description' => '搜索奖罚日志'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
