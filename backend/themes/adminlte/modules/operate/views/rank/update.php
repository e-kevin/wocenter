<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\Rank */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Ranks'), 'url' => ['/operate/rank']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/rank';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>