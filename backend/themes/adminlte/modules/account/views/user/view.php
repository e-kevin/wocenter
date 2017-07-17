<?php
use wocenter\libs\Constants;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wocenter\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['/account']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account';
?>

<?php \wonail\adminlte\widgets\Box::begin([
    'type' => \wonail\adminlte\AdminLTE::TYPE_PRIMARY,
    'leftToolbar' => '{goback}'
]) ?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'username',
        'email:email',
        [
            'attribute' => 'status',
            'value' => Constants::getUserStatusValue($model->status)
        ],
        [
            'attribute' => 'created_at',
            'format' => ['datetime']
        ],
        [
            'attribute' => 'updated_at',
            'format' => ['datetime']
        ],
        'userProfile.birthday',
    ],
])
?>

<?php \wonail\adminlte\widgets\Box::end() ?>
