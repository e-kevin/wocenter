<?php
namespace wocenter\backend\modules\modularity\models;

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
 * @property integer $run_module
 *
 * @property array $runModuleList 获取运行模块列表
 * @property array $validRunModuleList 获取有效的运行模块列表
 */
class Module extends ActiveRecord
{

    /**
     * @var integer 运行核心模块
     */
    const RUN_MODULE_CORE = 0;

    /**
     * @var integer 运行开发者模块
     */
    const RUN_MODULE_DEVELOPER = 1;

    /**
     * @var \wocenter\core\ModularityInfo 实例化模块信息类
     */
    public $infoInstance;

    /**
     * @var array 有效的运行模块列表
     */
    protected $_validRunModuleList;

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
            [['is_system', 'status', 'run_module'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['app'], 'string', 'max' => 15],
            [['app'], 'in', 'range' => array_keys(Yii::$app->params['appList'])],
            [['run_module'], 'in', 'range' => array_keys($this->getValidRunModuleList())],
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
            'run_module' => '运行模块',
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

    /**
     * 获取运行模块列表
     *
     * @return array
     */
    public function getRunModuleList()
    {
        return [
            self::RUN_MODULE_DEVELOPER => '开发者模块',
            self::RUN_MODULE_CORE => '核心模块',
        ];
    }

    /**
     * 获取有效的运行模块列表
     *
     * @return array
     */
    public function getValidRunModuleList()
    {
        return $this->_validRunModuleList ?: $this->getRunModuleList();
    }

    /**
     * 设置有效的运行模块列表
     *
     * @param $moduleList
     */
    public function setValidRunModuleList($moduleList)
    {
        $this->_validRunModuleList = $moduleList;
    }

}
