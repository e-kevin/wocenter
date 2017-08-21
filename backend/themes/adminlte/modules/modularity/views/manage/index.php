<?php

use wonail\adminlte\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ArrayDataProvider */

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
        'class' => 'kartik\grid\BooleanColumn',
        'value' => function ($model) {
            return $model['infoInstance']->isSystem;
        },
        'label' => '系统模块',
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
            '{goback}'
        ],
    ],
    'toolbar' => [
        [
            'content' => '{refresh}',
            'options' => [
                'class' => 'hide'
            ]
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
