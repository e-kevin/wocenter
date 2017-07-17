<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\models\User */

$this->title = '更新用户信息';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account';
?>

<?= $this->render('_form', [
    'model' => $model,
])
?>