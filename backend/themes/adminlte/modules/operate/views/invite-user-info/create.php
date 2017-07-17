<?php

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteUserInfo */

$this->title = Yii::t('wocenter/app', 'Create Invite User Info');
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Invite User Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/invite-user-info';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
