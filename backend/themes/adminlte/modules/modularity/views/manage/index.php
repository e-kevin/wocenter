<?php
use wonail\adminlte\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */
/* @var $runModuleList array */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '模块管理';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/modularity/manage/index';
//    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">新建模块</span>', ['/gii/default/view', 'id' => 'wc_module'], [
//        'class' => 'btn btn-success open-new',
//    ]);
}
// full_page:END
?>

<?php
$column = [
    [
        'label' => '模块ID',
        'attribute' => 'id',
        'format' => 'raw',
        'value' => function ($model) {
            return $model['infoInstance']->canInstall ? $model['id'] : Html::a($model['id'], ['update', 'id' => $model['id']], ['data-pjax' => 1]);
        },
    ],
    [
        'attribute' => 'name',
        'format' => 'html',
        'value' => function ($model) {
            if ($model['infoInstance']->isSystem) {
                return '<span class="text-danger">' . $model['infoInstance']->name . '</span>';
            } else {
                if ($model['infoInstance']->canInstall) {
                    return '<span class="text-success">' . $model['infoInstance']->name . '</span>';
                } else {
                    return '<span class="text-warning">' . $model['infoInstance']->name . '</span>';
                }
            }
        },
        'label' => '名称',
    ],
    [
        'attribute' => 'description',
        'value' => function ($model) {
            return $model['infoInstance']->description;
        },
        'label' => '描述',
    ],
//    [
//        'class' => 'kartik\grid\BooleanColumn',
//        'label' => '二次开发',
//        'value' => function ($model) {
//            return $model['disable_developer'];
//        },
//    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'value' => function ($model) {
            return $model['infoInstance']->isSystem;
        },
        'label' => '系统模块',
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'value' => function ($model) {
            return $model['core_module'];
        },
        'label' => '核心模块',
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'value' => function ($model) {
            return $model['developer_module'];
        },
        'width' => 'auto',
        'label' => '开发者模块',
    ],
//    [
//        'class' => 'kartik\grid\BooleanColumn',
//        'attribute' => 'status',
//        'value' => function ($model) {
//            return $model['status'];
//        },
//        'label' => '状态',
//    ],
    [
        'format' => 'html',
        'label' => '运行模块',
        'value' => function ($model) use ($runModuleList) {
            switch ($model['run_module']) {
                case \wocenter\models\Module::RUN_MODULE_CORE:
                    return '<span class="text-danger">' . $runModuleList[$model['run_module']] . '</span>';
                    break;
                case \wocenter\models\Module::RUN_MODULE_DEVELOPER:
                    return '<span class="text-warning">' . $runModuleList[$model['run_module']] . '</span>';
                    break;
                default:
                    return '未安装';
            }
        },
    ],
    [
        'attribute' => 'author',
        'value' => function ($model) {
            return $model['infoInstance']->developer;
        },
        'label' => '开发者',
    ],
    [
        'attribute' => 'version',
        'value' => function ($model) {
            return $model['infoInstance']->version;
        },
        'label' => '版本',
    ],
    [
        'class' => \wonail\adminlte\grid\ActionColumn::className(),
        'template' => '{install} {uninstall} {tips}',
        'visibleButtons' => [
            'install' => function ($model, $key, $index) {
                return $model['infoInstance']->canInstall;
            },
            'uninstall' => function ($model, $key, $index) {
                return $model['infoInstance']->canUninstall;
            },
            'tips' => function ($model, $key, $index) {
                return !$model['infoInstance']->canInstall && !$model['infoInstance']->canUninstall;
            },
        ],
        'buttons' => [
            'install' => function ($url, $model, $key) {
                return Html::a('安装', ['install', 'id' => $key], ['data-pjax' => 1]);
            },
            'uninstall' => function ($url, $model, $key) {
                return Html::a('卸载', ['uninstall', 'id' => $key], ['data-method' => 'post', 'data-confirm' => '确定要卸载所选模块吗？']);
            },
            'tips' => function ($url, $model, $key) {
                return 'N/A';
            },
        ],
    ],
];

echo GridView::widget([
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
            '{goback}',
        ],
    ],
    'toolbar' => [
        [
            'content' => '{refresh}',
            'options' => [
                'class' => 'hide',
            ],
        ],
        Html::a('清理缓存', ['clear-cache'], [
            'class' => 'btn btn-warning',
            'data-method' => 'post',
        ]),
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>
