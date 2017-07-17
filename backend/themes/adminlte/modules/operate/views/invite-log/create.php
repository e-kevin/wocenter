<?php

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteLog */

$this->title = Yii::t('wocenter/app', 'Create Invite Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Invite Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/invite-log';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
