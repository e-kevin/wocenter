<?php

namespace wocenter\actions;

use wocenter\{
    db\ActiveRecord, core\Dispatch, core\Model, traits\LoadModelTrait
};
use yii\base\Action;
use Yii;

/**
 * 根据模型`$modelClass`删除指定的单条数据
 *
 * @property \wocenter\core\Controller $controller
 */
class DeleteOne extends Action
{
    
    use LoadModelTrait;
    
    /**
     * @var string 需要操作的模型对象名, e.g. `User::className()`
     */
    public $modelClass = null;
    
    /**
     * @var boolean 是否标记删除而非真实删除，默认为标记删除
     */
    public $markAsDeleted = false;
    
    /**
     * @var string 标记删除的操作字段
     */
    public $deletedMarkAttribute = 'is_active';
    
    /**
     * @var integer 标记删除的值 e.g. 0为删除，1为激活
     */
    public $deletedMarkValue = 0;
    
    /**
     * @var string 接受数据的参数名
     */
    public $paramName = 'selection';
    
    /**
     * @var string 接受数据的提交方式
     */
    public $method = 'post';
    
    /**
     * @var string 操作成功后需要跳转的地址，默认为`Dispatch::RELOAD_LIST`，即成功后自动由js执行刷新操作
     */
    public $jumpUrl = Dispatch::RELOAD_LIST;
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        switch ($this->method) {
            case 'get':
                $id = $request->getQueryParam($this->paramName, null);
                break;
            default:
                $id = $request->getBodyParam($this->paramName, null);
                break;
        }
        if (empty($id)) {
            return $this->controller->error(Yii::t('wocenter/app', 'Select the data to be operated.'));
        }
    
        /** @var Model|ActiveRecord $model */
        $model = $this->loadModel($this->modelClass, $id);
        if ($this->markAsDeleted === true) {
            $model->setAttribute($this->deletedMarkAttribute, $this->deletedMarkValue);
            $res = $model->save(false);
        } else {
            $res = $model->delete();
        }
        
        if ($res) {
            return $this->controller->success($model->message ?: Yii::t('wocenter/app', 'Delete successful.'), $this->jumpUrl);
        } else {
            return $this->controller->error($model->message ?: Yii::t('wocenter/app', 'Delete failure.'), '', 2);
        }
    }
    
}