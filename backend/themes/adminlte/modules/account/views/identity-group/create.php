<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\IdentityGroup */

$this->title = '新增身份分组';
$this->params['breadcrumbs'][] = ['label' => '身份分组', 'url' => ['/account/identity-group']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity-group';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>