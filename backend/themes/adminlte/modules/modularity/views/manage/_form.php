<?php
use wonail\adminlte\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \wocenter\models\Module */
/* @var $form ActiveForm */

$footer = '<blockquote class="help-block">';
$footer .= '<p>系统模块：安装后无法卸载</p>';
if (Yii::$app->controller->action->id == 'install' && $model->infoInstance->canInstall) {
    $footer .= '<p>安装后，系统将自动同步更新所需菜单</p>';
} else {
    $footer .= '<p>更改设置后，系统将自动同步更新所需菜单</p>';
}
$footer .= '</blockquote>';
?>

<?php $form = ActiveForm::begin([
    'box' => [
        'footer' => $footer,
    ],
]);
?>

    <div class="jumbotron text-center">
        <h1><?= $model->infoInstance->name ?>
            <small class="text-danger">
                <?php if ($model->infoInstance->isSystem) : ?>系统模块<?php endif; ?>
            </small>
        </h1>
        <p class="lead"><?= $model->infoInstance->description ?></p>
        <?php
            $btn[] = Html::submitButton(Yii::t('wocenter/app',
                (Yii::$app->controller->action->id == 'install' && $model->infoInstance->canInstall)
                    ? 'Install'
                    : 'Save'
            ), ['class' => 'btn btn-success width-200']);
            $btn[] = Html::resetButton(Yii::t('wocenter/app', 'Reset'), ['class' => 'btn btn-default']);
            $btn[] = Html::button(Yii::t('wocenter/app', 'Go back'), ['class' => 'btn btn-default', 'data-widget' => 'goback',]);
            echo implode("\n", $btn);
        ?>
    </div>

<?php
// 系统模块
if (!$model->infoInstance->isSystem) {
    echo $form->field($model, 'is_system')->radioList(['否', '是']);
}
?>

<?php ActiveForm::end(); ?>