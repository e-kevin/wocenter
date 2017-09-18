<?php
namespace wocenter\frontend\core;

use Yii;
use yii\web\Application as baseApplication;

/**
 * 前台Application类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Application extends baseApplication
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        // 创建Wc实例
        Yii::$container->get('Wc');

        parent::init();
    }

}
