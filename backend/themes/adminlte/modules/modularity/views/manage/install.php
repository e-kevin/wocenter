<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\models\Module */

$this->title = '安装 ' . $model->id . ' 模块';
$this->params['breadcrumbs'][] = ['label' => '模块管理', 'url' => ['/modularity']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/modularity';
?>
<?=

$this->render('_form', [
    'model' => $model,
])
?>