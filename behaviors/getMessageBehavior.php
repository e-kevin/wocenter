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
 * 获取模型内反馈信息行为
 *
 * 主要用于在模型内执行方法并返回执行结果给前端时（如：controller控制器）提供友好的信息反馈
 * 该行为在AJAX提交方式返回结果时比较友好
 *
 * @property Model|ActiveRecord $owner
 * @author E-Kevin <e-kevin@qq.com>
 */
class getMessageBehavior extends Behavior
{

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Model::EVENT_AFTER_VALIDATE => 'afterValidate',
        ];
    }

    /**
     * @throws Exception
     */
    public function afterValidate()
    {
        if ($this->owner->hasErrors()) {
            // 格式化信息数组
            $errors = $this->owner->getFirstErrors();
            if (count($errors) > 1) {
                $i = 1;
                foreach ($errors as &$value) {
                    $value = $i++ . ') ' . $value;
                }
            }
            /**
             * 添加事务支持
             *
             * 如果存在事务操作且事务内的模型启用抛出异常，则把获取到的反馈信息以异常方式抛出，否则反馈信息由模型自行吞掉，
             * 此时可通过模型内`message`属性获取相关反馈信息
             */
            if (Yii::$app->getDb()->getIsActive() && (
                    $this->owner->throwException === true
                    || (Wc::getThrowException() && $this->owner->throwException !== false)
                )
            ) {
                throw new Exception(ArrayHelper::arrayToString($errors, ''));
            } else {
                $this->owner->message = ArrayHelper::arrayToString($errors, "</br>");
            }
        }
    }

}
