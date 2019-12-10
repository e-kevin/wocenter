<?php

namespace wocenter\models;

use wocenter\db\ActiveRecord;
use wocenter\interfaces\ConfigProviderInterface;

/**
 * This is the base model class for config table.
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $title
 * @property string $category_group
 * @property string $extra
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property string $value
 * @property integer $sort_order
 * @property string $rule
 */
class BaseConfigModel extends ActiveRecord implements ConfigProviderInterface
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'rule'], 'required'],
            [['type', 'category_group', 'created_at', 'updated_at', 'status', 'sort_order'], 'integer'],
            [['value', 'rule'], 'string'],
            [['name'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 50],
            [['extra'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '标识',
            'type' => '类型',
            'title' => '标题',
            'category_group' => '分组',
            'extra' => '额外数据',
            'remark' => '描述',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
            'value' => '默认值',
            'sort_order' => '排序',
            'rule' => '验证规则',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'name' => '只能使用英文且不能重复',
            'title' => '用于后台显示的配置标题',
            'sort_order' => '用于分组显示的顺序',
            'type' => '系统会根据不同类型解析配置数据',
            'category_group' => '不分组则不会显示在系统设置中',
            'extra' => '【下拉框、单选框、多选框】类型需要配置该项</br>多个可用英文符号 , ; 或换行分隔，如：</br>逗号 ,</br>key:value, key1:value1, key2:value2' .
                '</br>分号 ;</br>key:value; key1:value1; key2:value</br>换行</br>key:value</br>key1:value1</br>key2:value',
            'value' => '默认值',
            'remark' => '配置详细说明',
            'rule' => '配置验证规则</br>多条规则用英文符号 ; 或换行分隔，如：</br>分号 ;</br>required; string,max:10,min:4; string,length:1-3' .
                '</br>换行</br>required</br>string,max:10,min:4</br>string,length:1-3',
        ];
    }
    
}
