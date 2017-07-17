<?php
namespace wocenter\behaviors;

use Exception;
use wocenter\backend\core\Model;
use wocenter\core\ActiveRecord;
use wocenter\helpers\ArrayHelper;
use wocenter\Wc;
use Yii;
use yii\base\Behavior;

/**
 * 返回信息数据结果行为
 * 该行为在AJAX提交方式返回结果时比较友好
 *
 * @property Model|ActiveRecord $owner
 * @author E-Kevin <e-kevin@qq.com>
 */
class ReturnMessageBehavior extends Behavior
{

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Model::EVENT_AFTER_VALIDATE => 'afterValidate'
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function afterValidate()
    {
        if (!$this->owner->hasErrors()) {
            return true;
        }
        // 格式化信息数组
        $errors = $this->owner->getFirstErrors();
        if (count($errors) > 1) {
            $i = 1;
            foreach ($errors as &$value) {
                $value = $i++ . ') ' . $value;
            }
        }
        // 支持事务操作
        if (Yii::$app->getDb()->getIsActive() && ($this->owner->throwException === true
                || (Wc::getThrowException() && $this->owner->throwException !== false)
            )
        ) {
            throw new Exception(ArrayHelper::arrayToString($errors, ''));
        } else {
            $this->owner->message = ArrayHelper::arrayToString($errors, "</br>");
        }
    }

}
