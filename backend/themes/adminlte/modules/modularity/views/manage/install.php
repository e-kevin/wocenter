<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\modularity\models\Module */
/* @var $validRunModuleList array */

$this->title = '安装 ' . $model->id . ' 模块';
$this->params['breadcrumbs'][] = ['label' => '模块管理', 'url' => ['/modularity/manage/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/modularity/manage/index';
?>
<?=

$this->render('_form', [
    'model' => $model,
    'validRunModuleList' => $validRunModuleList,
])
?>