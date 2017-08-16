<?php
namespace wocenter\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%module}}".
 *
 * @property string $id
 * @property string $app
 * @property integer $is_system
 * @property integer $status
 */
class Module extends ActiveRecord
{

    /**
     * @var \wocenter\core\ModularityInfo 实例化模块信息类
     */
    public $infoInstance;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['is_system', 'status'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['app'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '模块ID',
            'app' => '所属应用',
            'is_system' => '系统模块',
            'status' => '状态',
        ];
    }

    /**
     * 获取当前应用已经安装的模块ID
     *
     * @return array 已经安装的模块ID
     */
    public function getInstalledModuleId()
    {
        return self::find()->select('id')->where(['app' => Yii::$app->id])->column();
    }

    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Wc::$service->getMenu()->syncMenus();
        Wc::$service->getModularity()->clearCache();
    }

}
