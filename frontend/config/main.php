<?php

return [
    'components' => [
        'i18n' => [
            'translations' => [
                'wocenter/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@wocenter/messages',
                ],
            ],
        ],
        'view' => [
            /**
             * wocenter默认使用调度服务响应控制器操作以进一步解耦控制器和操作方法，如'/site/index'路由将会从开发者调度器目录（
             * 由[[wocenter\core\View::getDeveloperThemePath()]]设置）获取'index'操作所对应的[[Index]]调度器来执行结果
             * 并根据视图映射（由[[wocenter\core\View::setPathMap()]]设置）定位操作所需渲染的视图文件。
             *
             * 实现调度功能的前提是必须确保系统调度层所调用的view组件是继承[[wocenter\core\View()]]类或其派生类
             *
             * @see wocenter\core\View::getDeveloperThemePath()
             * @see wocenter\core\View::setPathMap()
             */
            'class' => '\wocenter\core\View',
        ],
        'urlManager' => [
            'rules' => [
                '' => 'site/index',
            ],
        ],
    ],
];