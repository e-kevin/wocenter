<?php
/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\Identity */
/* @var $identityGroup array */
/* @var $profiles array */

$this->title = '新增身份';
$this->params['breadcrumbs'][] = ['label' => '身份列表', 'url' => ['/account/identity']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity';
?>

<?=
$this->render('_form', [
    'model' => $model,
    'identityGroup' => $identityGroup,
    'profiles' => $profiles,
])
?>