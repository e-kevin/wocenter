<?php
foreach (require_once(__DIR__ . '/../../config/classMap.php') as $old => $new) {
    Yii::$classMap[$old] = $new;
}
$captchaAction = '/passport/security/captcha'; // 验证码路由地址

return [
    'components' => [
        'user' => [
            'identityClass' => '\wocenter\backend\modules\account\models\User',
            'loginUrl' => ['/passport/common/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'class' => '\wocenter\core\View',
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
                '' => 'site/index',
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
        'captchaAction' => $captchaAction,
        'appList' => [
            'backend' => '后台应用',
            'frontend' => '前台应用',
            'console' => '控制台应用',
        ],
    ],
    'container' => [
        'definitions' => [
            'yii\captcha\Captcha' => [
                'captchaAction' => $captchaAction,
                'template' => '<div class="input-group">{input}<div class="input-group-addon">{image}</div></div>',
            ],
        ],
    ],
];
