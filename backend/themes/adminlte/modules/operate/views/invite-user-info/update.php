<?php

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteUserInfo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Invite User Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/invite-user-info';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>