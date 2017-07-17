<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\models\BackendUser */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?php
if ($model->getScenario() !== $model::SCENARIO_UPDATE) {
    echo $form->field($model, 'user_id')->textInput();
}
?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>