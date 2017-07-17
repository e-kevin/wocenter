<?php
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteLog */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'uid')->textInput() ?>

<?= $form->field($model, 'inviter_id')->textInput() ?>

<?= $form->field($model, 'invite_id')->textInput() ?>

<?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'created_at')->textInput() ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>