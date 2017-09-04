<?php
/* @var $this yii\web\View */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */

if ($generator->getIsCoreModule()) {
    $useClass = "use {$generator->getCoreModuleConfig()['moduleClass']} as baseModule;";
} else {
    $useClass = 'use wocenter\backend\core\Modularity as baseModule;';
}

echo "<?php\n";
?>
namespace <?= $generator->getNamespace() . ";\n" ?>

<?= $useClass . "\n" ?>

class Module extends baseModule
{
<?php if (!$generator->getIsCoreModule()) : ?>

    /**
    * @inheritdoc
    */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';
<?php endif; ?>

}
