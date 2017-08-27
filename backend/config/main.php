<?php
foreach (require_once(__DIR__ . '/../../config/classMap.php') as $old => $new) {
    Yii::$classMap[$old] = $new;
}
$captchaAction = '/passport/security/captcha'; // 验证码路由地址

return [
    'components' => [
        'user' => [
            'identityClass' => 'wocenter\models\User',
            'loginUrl' => ['/passport/common/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            /**
             * wocenter默认使用调度服务响应控制器操作以进一步解耦控制器和操作方法，如'/site/index'路由将会从调度器基础路径（
             * 由[[wocenter\services\DispatchService::getBasePath()]]设置）获取'index'操作所对应的[[Index]]调度器来执行
             * 结果并根据视图映射（由[[wocenter\core\View::setPathMap()]]设置）定位操作所需渲染的视图文件。
             *
             * 实现调度功能的一切基础必须指定系统调度层所调用的view组件必须继承wocenter\core\View类
             *
             * @see wocenter\services\DispatchService::getBasePath()
             * @see wocenter\core\View::setPathMap()
             */
            'class' => 'wocenter\backend\core\View',
            /**
             * 如果需要修改系统默认主题样式，我们建议你配置以下参数而不是直接修改系统文件以免更新时修改被覆盖。
             * 具体参考[[wocenter\core\View::getThemePath()]]方法的实现逻辑和[[wocenter\core\View::setPathMap()]]
             * 方法里的路径映射配置`pathMap`，这可让你花更多的精力去专注主题样式的修改而不需要对系统做过多的改动。
             *
             * 如下面的例子是把系统默认主题路径变更为当前应用目录下的`themes`文件夹
             *
             * @see \wocenter\core\View::getBasePath()
             */
//            'basePath' => '@app/themes', // 主题別名路径，必须以'@'开头
        ],
        'i18n' => [
            'translations' => [
                'wocenter/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@wocenter/messages',
                ],
            ],
        ],
        'urlManager' => [
            'rules' => [
                '/' => 'site/index',
            ],
        ],
    ],
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
            'autoWidgetSettings' => [
                'date' => [
                    'pluginOptions' => [
                        'autoclose' => true,
                    ],
                ],
                'datetime' => [
                    'pluginOptions' => [
                        'showMeridian' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                    ],
                ],
                'time' => [
                    'pluginOptions' => [
                        'autoclose' => true,
                    ],
                ],
            ],
        ],
    ],
    'params' => [
        'redirect' => 'redirect', // url跳转地址参数
        'captchaAction' => $captchaAction
    ],
    'container' => [
        'definitions' => [
            'yii\captcha\Captcha' => [
                'captchaAction' => $captchaAction,
                'template' => '<div class="input-group">{input}<div class="input-group-addon">{image}</div></div>',
            ]
        ],
    ],
];
