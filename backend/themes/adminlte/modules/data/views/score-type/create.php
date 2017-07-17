<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\data\models\UserScoreType */

$this->title = '创建积分类型';
$this->params['breadcrumbs'][] = ['label' => '积分类型', 'url' => ['/data/score-type']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/data/score-type';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
