<?php
namespace wocenter\backend\modules\core;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->id = 'core';
        $this->name = '系统框架管理';
        $this->description = '提供系统基础功能';
        $this->version = '1.0';
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
            // 扩展中心
            [
                'name' => '扩展中心',
                'icon_html' => 'cube',
                'modularity' => 'core',
                'show_on_menu' => true,
            ],
            // 人事管理
            [
                'name' => '人事管理',
                'icon_html' => 'user',
                'modularity' => 'core',
                'show_on_menu' => true,
            ],
            // 系统管理
            [
                'name' => '系统管理',
                'icon_html' => 'cog',
                'modularity' => 'core',
                'show_on_menu' => true,
            ],
            // 安全管理
            [
                'name' => '安全管理',
                'icon_html' => 'shield',
                'modularity' => 'core',
                'show_on_menu' => true,
            ],
            // 运营管理
            [
                'name' => '运营管理',
                'icon_html' => 'line-chart',
                'modularity' => 'core',
                'show_on_menu' => true,
            ],
        ];
    }

}
