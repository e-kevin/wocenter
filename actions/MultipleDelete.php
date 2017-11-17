<?php

namespace wocenter\actions;

use wocenter\core\{
    ActiveRecord, Dispatch
};
use yii\base\{
    Action, InvalidConfigException
};
use Yii;

/**
 * Action for multiple deletion of models from backend grid
 *
 * @property \wocenter\core\Controller $controller
 */
class MultipleDelete extends Action
{
    
    /**
     * @var string 需要操作的模型对象名, e.g.. `User::className()`
     */
    public $modelClass = null;
    
    /**
     * @var boolean 是否标记删除而非真实删除
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
     * @var string 删除参数名
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
    public function init()
    {
        if (!isset($this->modelClass)) {
            throw new InvalidConfigException("The property `modelClass` should be set in controller actions");
        }
        if (!class_exists($this->modelClass)) {
            throw new InvalidConfigException("Model class `{$this->modelClass}` does not exists");
        }
    }
    
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
        
        $res = false;
        $message = '';
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        /** @var ActiveRecord[] $items */
        $items = $modelClass::findAll($id);
        foreach ($items as $item) {
            if ($this->markAsDeleted === true) {
                $item->setAttribute($this->deletedMarkAttribute, $this->deletedMarkValue);
                $res = $item->save(false);
            } else {
                $res = $item->delete();
            }
            if (!$res) {
                $message = $item->message;
                break;
            }
        }
        
        if ($res) {
            return $this->controller->success($message ?: Yii::t('wocenter/app', 'Delete successful.'), $this->jumpUrl);
        } else {
            return $this->controller->error($message ?: Yii::t('wocenter/app', 'Delete failure.'), '', 2);
        }
    }
    
}