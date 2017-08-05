<?php
namespace wocenter\backend\modules\menu\models;

use wocenter\behaviors\TreeBehavior;
use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $category_id
 * @property string $name
 * @property string $alias_name
 * @property string $url
 * @property string $full_url
 * @property string $params
 * @property string $target
 * @property string $description
 * @property integer $status
 * @property integer $is_dev
 * @property integer $sort_order
 * @property string $icon_html
 * @property integer $created_type
 * @property string $modularity
 * @property integer $show_on_menu
 *
 * @property array $createdTypeList 获取创建方式值
 *
 * 行为方法属性
 * @property string $breadcrumbParentParam
 * @method TreeBehavior getChildrenIds()
 * @method TreeBehavior getParentIds()
 * @method TreeBehavior getTreeSelectList()
 * @method TreeBehavior getBreadcrumbs()
 */
class Menu extends ActiveRecord
{

    /**
     * 缓存所有菜单项
     */
    const CACHE_ALL_MENU = 'allMenus';

    /**
     * 菜单创建者为用户
     */
    const CREATE_TYPE_BY_USER = 0;

    /**
     * 菜单创建者为模块
     */
    const CREATE_TYPE_BY_MODULE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => TreeBehavior::className(),
            'showTitleField' => 'alias_name',
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url', 'full_url', 'target', 'category_id', 'parent_id'], 'required'],
            [['status', 'sort_order', 'is_dev', 'target', 'created_type', 'show_on_menu', 'parent_id'], 'integer'],
            [['name', 'category_id', 'alias_name'], 'string', 'max' => 64],
            ['alias_name', 'default', 'value' => function ($model, $attribute) {
                return empty($model->$attribute) ? $model->name : $model->$attribute;
            }],
            [['url', 'full_url', 'description'], 'string', 'max' => 512],
            ['icon_html', 'string', 'max' => 30],
            ['params', 'string', 'max' => 200],
            ['modularity', 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['category_id', 'parent_id', 'name', 'alias_name', 'url', 'full_url', 'params',
                'target', 'status', 'sort_order', 'is_dev', 'description', 'icon_html', 'show_on_menu'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => '父节点',
            'category_id' => '分类',
            'name' => '名称',
            'alias_name' => '菜单别名',
            'url' => '菜单显示地址',
            'full_url' => '完整URL地址',
            'params' => 'URL参数',
            'target' => '打开方式',
            'description' => '描述',
            'is_dev' => '开发模式可见',
            'icon_html' => '图标',
            'status' => '状态',
            'sort_order' => '排序',
            'created_type' => '创建方式',
            'createdTypeValue' => '创建方式',
            'show_on_menu' => '显示菜单',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'alias_name' => "为空时默认为`{$this->getAttributeLabel('name')}`的值",
            'url' => '一般为简写路由地址',
            'full_url' => '权限验证时以此路由做判断',
            'target' => '建设中……',
        ];
    }

    /**
     * 获取数据库所有菜单数据
     *
     * @param integer|boolean $duration 缓存周期，默认缓存`60`秒
     *
     * @return mixed
     */
    public function getAll($duration = 60)
    {
        return Wc::getOrSet(self::CACHE_ALL_MENU, function () {
            return self::find()->orderBy('sort_order ASC')->asArray()->all() ?: [];
        }, $duration);
    }

    /**
     * 获取创建方式列表
     *
     * @return array
     */
    public function getCreatedTypeList()
    {
        return [
            self::CREATE_TYPE_BY_USER => '用户',
            self::CREATE_TYPE_BY_MODULE => '模块',
        ];
    }

    /**
     * 获取创建方式值
     *
     * @return string
     */
    public function getCreatedTypeValue()
    {
        return $this->createdTypeList[$this->created_type];
    }

    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Yii::$app->getCache()->delete(self::CACHE_ALL_MENU);
    }

}
