<?php
use wocenter\models\User;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model User */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'status')->radioList([
    User::STATUS_FORBIDDEN => Yii::t('wocenter/app', 'User disabled status'),
    User::STATUS_ACTIVE => Yii::t('wocenter/app', 'User active status'),
]) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>