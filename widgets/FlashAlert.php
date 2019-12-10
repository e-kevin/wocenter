<?php

namespace wocenter\widgets;

use rmrevin\yii\fontawesome\component\Icon;
use Yii;
use yii\bootstrap\Alert;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * FlashAlert 组件从Yii::$app->session->getAllFlashes()处收集信息并以 \yii\bootstrap\Alert 组件显示出来
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * 支持数组信息
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 * todo 允许自定义header、icon
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class FlashAlert extends Widget
{
    
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var boolean 是否显示头部标题，默认不显示
     */
    public $showHeader = false;

    /**
     * @var array 提醒类型定义
     */
    protected $alertTypes = [
        'error' => [
            'class' => 'alert-danger',
            'header' => 'Error!',
            'icon' => 'ban',
        ],
        'danger' => [
            'class' => 'alert-danger',
            'header' => 'Danger!',
            'icon' => 'bug',
        ],
        'success' => [
            'class' => 'alert-success',
            'header' => 'Success!',
            'icon' => 'check',
        ],
        'info' => [
            'class' => 'alert-info',
            'header' => 'Info!',
            'icon' => 'info-circle',
        ],
        'warning' => [
            'class' => 'alert-warning',
            'header' => 'Warning!',
            'icon' => 'warning',
        ],
    ];

    /**
     * @var array the options for rendering the close button tag.
     * The close button is displayed in the header of the modal window. Clicking
     * on the button will hide the modal window. If this is false, no close button will be rendered.
     *
     * The following special options are supported:
     *
     * - tag: string, the tag name of the button. Defaults to 'button'.
     * - label: string, the label of the button. Defaults to '&times;'.
     *
     * The rest of the options will be rendered as the HTML attributes of the button tag.
     * Please refer to the [Alert documentation](http://getbootstrap.com/components/#alerts)
     * for the supported HTML attributes.
     */
    public $closeButton = [];

    public function init()
    {
        parent::init();

        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';

        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $alterTypes = $this->alertTypes[$type];
                $header = $this->showHeader ? Html::tag('h4', (isset($alterTypes['icon']) ? new Icon($alterTypes['icon']) . '&nbsp;' : '') . $alterTypes['header']) : '';
                $data = (array) $data;
                // 支持数组格式的信息提醒
                foreach ($data as $i => $message) {
                    $this->options['class'] = $alterTypes['class'] . $appendCss;
                    $this->options['id'] = $type . '-' . $this->getId() . '-' . $i;

                    echo Alert::widget([
                        'body' => $header . $message,
                        'closeButton' => $this->closeButton,
                        'options' => $this->options,
                    ]);
                }

                $session->removeFlash($type);
            }
        }
    }

}
