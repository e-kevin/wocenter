<?php
namespace wocenter\core;

use wocenter\behaviors\getMessageBehavior;
use wocenter\traits\ExtendModelTrait;
use yii\base\Model as baseModel;

/**
 * 基础Model类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Model extends baseModel
{

    use ExtendModelTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            getMessageBehavior::className(),
        ];
    }

}
