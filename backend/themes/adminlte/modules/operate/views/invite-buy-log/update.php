<?php

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteBuyLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Invite Buy Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = 'invite-buy-log';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>