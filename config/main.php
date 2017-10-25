<?php

return [
    'container' => [
        'definitions' => [
            'Wc' => 'wocenter\Wc',
        ],
    ],
    'aliases' => [
        '@wocenter' => '@vendor/wonail/wocenter',
    ],
    'components' => [
        'commonCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache',
        ],
        'notificationService' => [
            'class' => 'wocenter\services\NotificationService',
            'subService' => [
                'message' => ['class' => 'wocenter\services\notification\MessageService'],
                'sms' => ['class' => 'wocenter\services\notification\SmsService'],
                'email' => ['class' => 'wocenter\services\notification\EmailService'],
            ],
        ],
        'logService' => [
            'class' => 'wocenter\services\LogService',
        ],
        'passportService' => [
            'class' => 'wocenter\services\PassportService',
            'subService' => [
                'ucenter' => ['class' => 'wocenter\services\passport\UcenterService'],
                'verify' => ['class' => 'wocenter\services\passport\VerifyService'],
                'validation' => ['class' => 'wocenter\services\passport\ValidationService'],
            ],
        ],
        'systemService' => [
            'class' => 'wocenter\services\SystemService',
            'subService' => [
                'config' => ['class' => 'wocenter\services\system\ConfigService'],
            ],
        ],
        'menuService' => [
            'class' => 'wocenter\services\MenuService',
        ],
        'actionService' => [
            'class' => 'wocenter\services\ActionService',
        ],
        'accountService' => [
            'class' => 'wocenter\services\AccountService',
        ],
        'extensionService' => [
            'class' => 'wocenter\services\ExtensionService',
            'subService' => [
                'controller' => ['class' => 'wocenter\services\extension\ControllerService'],
                'modularity' => ['class' => 'wocenter\services\extension\ModularityService'],
                'load' => ['class' => 'wocenter\services\extension\LoadService'],
                'theme' => ['class' => 'wocenter\services\extension\ThemeService'],
            ],
        ],
    ],
];
