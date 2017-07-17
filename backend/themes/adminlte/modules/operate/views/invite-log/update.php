<?php

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Invite Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/invite-log';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>