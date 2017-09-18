<?php
use wocenter\Wc;
use wonail\adminlte\widgets\FlashAlert;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

wonail\adminlte\assetBundle\AdminLteAsset::register($this);
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <?php
        $this->registerMetaTag([
            'charset' => Yii::$app->charset,
        ]);
        $this->registerMetaTag([
            'http-equiv' => 'X-UA-Compatible',
            'content' => 'IE=edge',
        ]);
        $this->registerMetaTag([
            'name' => 'viewport',
            'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no',
        ]);
        $this->registerMetaTag([
            'name' => 'description',
            'content' => Html::encode(Wc::$service->getSystem()->getConfig()->get('WEB_SITE_DESCRIPTION')),
        ], 'description');
        $this->registerMetaTag([
            'name' => 'keywords',
            'content' => Html::encode(Wc::$service->getSystem()->getConfig()->get('WEB_SITE_KEYWORD')),
        ], 'keywords');
        echo Html::csrfMetaTags();
        echo Html::tag('title', Html::encode(Yii::$app->name));
        ?>
        <?php $this->head() ?>
    </head>

    <body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>

    <?= FlashAlert::widget() ?>

    <?= $content ?>

    <?= wonail\scrolltop\ScrollTop::widget() ?>

    <div class="m-10 text-center">
        <strong>Copyright &copy; <?= Yii::$app->params['app.copyright'] . ' ' . Yii::$app->params['app.name'] ?>
            .</strong>
        All rights reserved. <?= Wc::$service->getSystem()->getConfig()->get('WEB_SITE_ICP') ?>
    </div>


    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>