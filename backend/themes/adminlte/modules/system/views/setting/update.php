<?php
use wocenter\Wc;
use yii\web\View;

/* @var $this View */

$title = Wc::$service->getSystem()->getConfig()->extra('CONFIG_GROUP_LIST');
$this->title = $title[$id] . '配置';
$this->params['breadcrumbs'][] = '网站设置';
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/system/setting/' . $this->context->action->id;
?>

<?= $this->render('_form', [
    'models' => $models,
])
?>