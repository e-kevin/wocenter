<?php
$captchaAction = '/passport/security/captcha'; // 验证码路由地址

return [
    'components' => [
        'user' => [
            'identityClass' => '\wocenter\backend\modules\account\models\BaseUser',
            'loginUrl' => ['/passport/common/login'],
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
        'view' => [
            'class' => '\wocenter\core\View',
        ],
        'urlManager' => [
            'rules' => [
                '' => 'site/index',
            ],
        ],
    ],
    'params' => [
        'redirect' => 'redirect', // url跳转地址参数
        'captchaAction' => $captchaAction,
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
