<?php

namespace wocenter\dispatches;

use wocenter\{
    db\ActiveRecord, traits\LoadModelTrait, Wc
};

/**
 * 根据模型`$modelClass`删除指定的单条数据
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DeleteOne extends BaseDelete
{
    
    use LoadModelTrait;
    
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
        $model = $this->loadModel($this->modelClass, $id);
        $res = Wc::transaction(function () use ($model) {
            if ($this->markAsDeleted === true) {
                $model->setAttribute($this->deletedMarkAttribute, $this->deletedMarkValue);

                return $model->save(false);
            } else {
                return $model->delete();
            }
        });
        
        return $this->response($res, $model->_message);
    }
    
}