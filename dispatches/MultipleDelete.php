<?php

namespace wocenter\dispatches;

use wocenter\db\ActiveRecord;
use wocenter\helpers\StringHelper;
use wocenter\Wc;
use yii\base\InvalidConfigException;

/**
 * 根据模型`$modelClass`删除指定的多条数据
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class MultipleDelete extends BaseDelete
{
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->modelClass)) {
            throw new InvalidConfigException("The property `modelClass` should be set in controller `\$defaultDispatches`.");
        }
        if (!class_exists($this->modelClass)) {
            throw new InvalidConfigException("Model class `{$this->modelClass}` does not exists");
        }
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $res = false;
        $message = '';
        /** @var ActiveRecord[] $items */
        $items = $this->modelClass::findAll(StringHelper::parseIds($id));
        foreach ($items as $item) {
            $res = Wc::transaction(function () use ($item) {
                if ($this->markAsDeleted === true) {
                    $item->setAttribute($this->deletedMarkAttribute, $this->deletedMarkValue);
                    
                    return $item->save(false);
                } else {
                    return $item->delete();
                }
            });
            if (!$res) {
                $message = $item->_message;
                break;
            }
        }
        
        return $this->response($res, $message);
    }
    
}