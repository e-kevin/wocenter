<?php
namespace wocenter\backend\modules\modularity;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();

        $this->name = '模块管理';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '对系统中的模块进行管理';
        $this->isSystem = true;
    }

    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            [
                'name' => '扩展中心',
                'items' => [
                    // 模块管理
                    [
                        'name' => '模块管理',
                        'icon_html' => 'cubes',
                        'modularity' => $this->getId(),
                        'show_on_menu' => true,
                        'items' => [
                            [
                                'name' => '模块列表',
                                'url' => "/{$this->getId()}/manage/index",
                                'description' => '模块列表',
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->getId()}/manage/index", 'description' => '模块列表'],
                                    ['name' => '卸载', 'url' => "/{$this->getId()}/manage/uninstall", 'description' => '卸载模块'],
                                    ['name' => '安装', 'url' => "/{$this->getId()}/manage/install", 'description' => '安装模块'],
                                    ['name' => '编辑', 'url' => "/{$this->getId()}/manage/update", 'description' => '更新模块'],
                                ],
                            ],
                            ['name' => '清理模块缓存', 'url' => "/{$this->getId()}/manage/clear-cache",
                                'description' => '清理模块缓存', 'show_on_menu' => true
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
