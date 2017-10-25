<?php

namespace wocenter\grid;

use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

class DateTimeColumn extends DataColumn
{
    
    public $format = 'datetime';
    
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                // 不存在则显示返回null，主要是用于更正时间日期的显示，因为时间为0时同样会被格式化
                return ArrayHelper::getValue($model, $this->value) ?: null;
            } else {
                return call_user_func($this->value, $model, $key, $index, $this);
            }
        } elseif ($this->attribute !== null) {
            // 不存在则显示返回null，主要是用于更正时间日期的显示，因为时间为0时同样会被格式化
            return ArrayHelper::getValue($model, $this->attribute) ?: null;
        }
        
        return null;
    }
    
}
