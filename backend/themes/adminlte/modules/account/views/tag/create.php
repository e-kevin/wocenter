<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\Tag */
/* @var $tagList array 标签列表 */
/* @var $breadcrumbs array 面包屑导航 */
/* @var $title string 当前面包屑标题 */

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['navSelectPage'] = '/account/tag';
?>

<?= $this->render('_form', [
    'model' => $model,
    'tagList' => $tagList
]) ?>