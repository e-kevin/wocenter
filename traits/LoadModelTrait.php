<?php

namespace wocenter\traits;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Class LoadModelTrait
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait LoadModelTrait
{
    
    /**
     * 根据模型主键获取相关数据，如果数据不存在，则抛出404异常
     *
     * @param \yii\db\ActiveRecord|string $modelClass 模型对象
     * @param integer $id 主键ID
     * @param boolean $throwException 是否允许抛出异常
     * @param array $config 配置模型属性 e.g. ['scenario' => 'update']
     *
     * @return null|\yii\db\ActiveRecord
     * @throws InvalidConfigException
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function loadModel($modelClass, $id, $throwException = true, $config = [])
    {
        if (!class_exists($modelClass)) {
            throw new InvalidConfigException("Model Class `{$modelClass}` does not exists");
        }
        $model = $modelClass::findOne($id);
        if (!is_object($model)) {
            if ($throwException) {
                throw new NotFoundHttpException('The requested page does not exist.');
            } else {
                return null;
            }
        }
        if (!empty($config)) {
            Yii::configure($model, $config);
        }
        
        return $model;
    }
    
}
