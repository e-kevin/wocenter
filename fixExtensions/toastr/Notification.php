<?php
namespace lavrentiev\widgets\toastr;

use yii\helpers\Html;
use yii\base\Widget;
use yii\helpers\Json;
use lavrentiev\widgets\toastr\assets\ToastrAsset;

class Notification extends Widget
{
    /** @var string $title */
    public $title;

    /** @var string $message */
    public $message;

    /** @var string $type */
    public $type;

    /** @var string $types */
    public $types = ['info', 'error', 'success', 'warning'];

    /** @var string $typeDefault */
    public $typeDefault = 'info';

    /** @var array $options */
    public $options = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->view->registerAssetBundle(ToastrAsset::className());

        $this->options = Json::encode($this->options); // 主要修复该行

        $this->title = ($this->title) ? Html::encode($this->title) : '';

        $this->message = ($this->message) ? Html::encode($this->message) : 'This is the message';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (in_array($this->type, $this->types)) {

            $this->view->registerJs("toastr.{$this->type}(\"{$this->message}\", \"{$this->title}\", {$this->options});");

        } else {

            $this->view->registerJs("toastr.{$this->typeDefault}(\"{$this->message}\", \"{$this->title}\", {$this->options});");

        }
    }
}
