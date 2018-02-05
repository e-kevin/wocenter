<?php

namespace wocenter\db;

/**
 * This is the base model class for extension table.
 *
 * @property string $id
 * @property string $extension_name
 * @property integer $is_system
 * @property integer $status
 * @property integer $run
 */
class BaseExtensionModel extends ActiveRecord
{
    
    /**
     * @var \wocenter\core\Extension 实例化扩展信息类
     */
    public $infoInstance;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'extension_name'], 'required'],
            [['is_system', 'status', 'run'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['extension_name'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'extension_name' => '扩展名称',
            'is_system' => '核心扩展',
            'status' => '状态',
            'run' => '运行模式',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'is_system' => '安装后无法卸载',
            'run' => '选择哪个扩展配置来运行当前扩展',
        ];
    }
    
    /**
     * 获取已经安装的扩展
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalled()
    {
        return self::find()->asArray()->indexBy('extension_name')->all();
    }
    
}
