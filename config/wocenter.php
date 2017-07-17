<?php
foreach (require_once('classMap.php') as $old => $new) {
    Yii::$classMap[$old] = $new;
}
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
            'class' => 'wocenter\backend\core\View', // 系统调度层所调用的view组件必须继承wocenter\core\View类
            /**
             * 如果需要修改系统默认主题样式，我们建议你配置以下参数而不是直接修改系统文件以免更新时修改被覆盖，
             * 具体参考`wocenter\core\View[[getThemePath()]]`方法的实现逻辑和[[setTheme()]]方法里的路径映射配置`pathMap`，
             * 这可让你花更多的精力去专注主题样式的修改而不需要对系统做过多的改动。如下把系统默认主题路径改为当前应用目录下
             * 的`themes`文件夹
             * @see \wocenter\core\View::getBasePath()
             */
//            'basePath' => '@app/themes',
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
            'enablePrettyUrl' => true,
            'showScriptName' => true,
//            'suffix' => '.html',
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
        'redirect' => 'redirect',
    ],
    'container' => [
        'definitions' => [
            'Wc' => 'wocenter\Wc',
        ],
    ],
];
