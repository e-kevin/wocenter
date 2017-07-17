<?php
namespace wocenter\core;

use wocenter\behaviors\ReturnMessageBehavior;
use wocenter\traits\ExtendModelTrait;
use yii\db\ActiveRecord as baseActiveRecord;

/**
 * 基础ActiveRecord类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ActiveRecord extends baseActiveRecord
{

    use ExtendModelTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ReturnMessageBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $this->clearCache();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->clearCache();
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {

    }
    
}
