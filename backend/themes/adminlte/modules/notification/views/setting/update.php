<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\notification\models\Notify */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '通知管理', 'url' => ['/notification']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/notification';
?>
<?=

$this->render('_form', [
    'model' => $model,
])
?>