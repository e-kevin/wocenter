<?php
/* @var $this yii\web\View */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */

if ($generator->getIsCoreModule()) {
    /** @var \wocenter\core\ModularityInfo $infoClass */
    $infoClass = $generator->getCoreModuleConfig()['infoInstance'];
    $useClass = "use {$infoClass->className()} as baseInfo;";
} else {
    $useClass = 'use wocenter\core\ModularityInfo as baseInfo;';
}

echo "<?php\n";
?>
namespace <?= $generator->getNamespace() . ";\n" ?>

<?= $useClass . "\n" ?>

class Info extends baseInfo
{
<?php if (!$generator->getIsCoreModule()) : ?>

    /**
    * @inheritdoc
    */
    public $name = '<?= $generator->moduleID ?>';

    /**
    * @inheritdoc
    */
    public $description = '<?= $generator->moduleID ?> description';

    /**
    * @inheritdoc
    */
    public $developer = 'Developer';
<?php endif; ?>

    /**
    * @inheritdoc
    */
    public $type = 'developer';

}
