<?php
namespace wocenter\behaviors;

use wocenter\helpers\DateTimeHelper;
use wocenter\widgets\DateControl;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * 自动填充特定属性值为当前时间戳或自定义日期时间
 *
 * - 通常情况下该行为不需要修改用户原有的规则和场景配置数据，用户仅需做以下简单改动即可：
 *  - 1、在[[$this->owner->rules()]]方法里使用$this->createRules()方法返回结果，行为会根据行为配置自动处理规则
 *  - 2、在[[$this->owner->scenarios()]]方法里使用$this->createScenarios()方法返回结果，行为会根据行为配置自动处理场景
 *
 * @property ActiveRecord $owner
 * @property string $displayTimeZone
 * @property string $saveTimeZone
 *
 * 具体使用方法可参考以下文件
 * @see Identity
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ModifyTimestampBehavior extends TimestampBehavior
{

    /**
     * @var boolean 是否允许自定义更新[[$updatedAtAttribute]]字段值，默认为`false` - 不允许。
     * 默认配置时，行为将根据[[$attributes]]属性配置处理相关时间日期字段值。如果未自定义[[$attributes]]值，
     * 则每次新建数据或更新数据时，[[$updatedAtAttribute]]属性值都会被赋予当前时间戳。
     */
    public $modifyUpdatedAt = false;

    /**
     * @var boolean 是否允许自定义更新[[$createdAtAttribute]]字段值，默认为`false` - 不允许。
     * 默认配置时，行为将根据[[$attributes]]属性配置处理相关时间日期字段值。如果未自定义[[$attributes]]值，
     * 则每次新建数据时，[[$createdAtAttribute]]属性值都会被赋予当前时间戳。
     */
    public $modifyCreatedAt = false;

    /**
     * @var string [[$createdAtAttribute]]属性使用哪个小部件显示，默认为`DateControl::FORMAT_DATETIME`，可用值有
     * [[DateControl::FORMAT_DATETIME]], [[DateControl::FORMAT_DATE]], [[DateControl::FORMAT_TIME]]或
     * `date`, `time`, `datetime`。
     *
     * @see kartik\datecontrol\DateControl
     */
    public $createdAtDisplayType = DateControl::FORMAT_DATETIME;

    /**
     * @var string [[$updatedAtAttribute]]属性使用哪个小部件显示，默认为`DateControl::FORMAT_DATETIME`，可用值有
     * [[DateControl::FORMAT_DATETIME]], [[DateControl::FORMAT_DATE]], [[DateControl::FORMAT_TIME]]或
     * `date`, `time`, `datetime`。
     *
     * @see kartik\datecontrol\DateControl
     */
    public $updatedAtDisplayType = DateControl::FORMAT_DATETIME;

    /**
     * @var string [[$updateAtAttribute]]属性的显示格式，支持php和ICU时间日期格式
     * 当该值未设置时，默认根据[[$updatedAtDisplayType]]值从[[$_displayFormats]]数组里获取数据
     *
     * 配置示例：
     * ```php
     * 'MM/dd/yyyy' // date in ICU format
     * 'php:m/d/Y' // the same date in PHP format
     * ```
     */
    public $updatedAtDisplayFormat;

    /**
     * @var string [[$createdAtAttribute]]属性的显示格式，支持php和ICU时间日期格式
     * 当该值未设置时，默认根据[[$createdAtDisplayType]]值从[[$_displayFormats]]数组里获取数据
     *
     * 配置示例：
     * ```php
     * 'MM/dd/yyyy' // date in ICU format
     * 'php:m/d/Y' // the same date in PHP format
     * ```
     */
    public $createdAtDisplayFormat;

    /**
     * @var string [[$updateAtAttribute]]属性的保存格式，支持php和ICU时间日期格式
     * 当该值未设置时，默认根据[[$updatedAtDisplayType]]值从[[$_saveFormats]]数组里获取数据
     *
     * 配置示例：
     * ```php
     * 'MM/dd/yyyy' // date in ICU format
     * 'php:m/d/Y' // the same date in PHP format
     * 'php:U' // 时间戳格式
     * ```
     */
    public $updatedAtSaveFormat;

    /**
     * @var string [[$createdAtAttribute]]属性的显示格式，支持php和ICU时间日期格式
     * 当该值未设置时，默认根据[[$createdAtDisplayType]]值从[[$_saveFormats]]数组里获取数据
     *
     * 配置示例：
     * ```php
     * 'MM/dd/yyyy' // date in ICU format
     * 'php:m/d/Y' // the same date in PHP format
     * 'php:U' // 时间戳格式
     * ```
     */
    public $createdAtSaveFormat;

    /**
     * @var string 使用哪个小部件显示时间日期选项，行为会根据该值自动添加相应的验证规则
     */
    public $dateTimeWidget = '\wocenter\widgets\DateControl';

    /**
     * @var array 时间日期默认显示格式。当[[$updatedAtDisplayFormat]]或[[$createdAtDisplayFormat]]未设置时，将默认根据
     * [[$createdAtDisplayType]]或[[$updatedAtDisplayType]]值获取相关数据
     */
    private $_displayFormats = [
        DateControl::FORMAT_DATE => 'php:Y-m-d',
        DateControl::FORMAT_TIME => 'php:H:i:s a',
        DateControl::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
    ];

    /**
     * @var array 时间日期默认显示格式。当[[$updatedAtSaveFormat]]或[[$createdAtSaveFormat]]未设置时，将默认根据
     * [[$createdAtDisplayType]]或[[$updatedAtDisplayType]]值获取相关数据
     */
    private $_saveFormats = [
        DateControl::FORMAT_DATE => 'php:U',
        DateControl::FORMAT_TIME => 'php:H:U',
        DateControl::FORMAT_DATETIME => 'php:U',
    ];

    /**
     * @inheritdoc
     */
    public function evaluateAttributes($event)
    {
        if ($this->skipUpdateOnClean
            && $event->name == ActiveRecord::EVENT_BEFORE_UPDATE
            && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }

        if (!empty($this->attributes[$event->name])) {
            $attributes = (array)$this->attributes[$event->name];
            $value = $this->getValue($event);
            foreach ($attributes as $attribute) {
                if (
                    is_string($attribute) && (
                        ($attribute == $this->createdAtAttribute && !$this->modifyCreatedAt)
                        || ($attribute == $this->updatedAtAttribute && !$this->modifyUpdatedAt)
                    )
                ) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }

    /**
     * 创建验证规则
     * 根据配置自动添加$this->createdAtAttribute和$this->updatedAtAttribute属性的验证规则，同时滤掉该行为的其他规则
     *
     * @param array $modelRules 模型验证规则
     *
     * @return array 验证规则
     */
    public function createRules(array $modelRules)
    {
        // 删除规则里有关时间日期字段的所有规则，由系统判断是否需要添加相应的规则。如需对时间日期字段添加其他规则，
        // 可合并需要的规则到该方法调用后返回的数据结果里
        foreach ($modelRules as $key => &$rule) {
            if (is_array($rule[0])) {
                foreach ($rule[0] as $k => $v) {
                    if (in_array($v, [$this->createdAtAttribute, $this->updatedAtAttribute])) {
                        unset($rule[0][$k]);
                    }
                }
                if (count($rule[0]) === 0) {
                    unset($modelRules[$key]);
                }
            } else {
                if (in_array($rule[0], [$this->createdAtAttribute, $this->updatedAtAttribute])) {
                    unset($modelRules[$key]);
                }
            }
        }
        // 添加相应规则
        $rules = [];
        $attributes = [
            [
                $this->createdAtAttribute,
                $this->modifyCreatedAt,
            ],
            [
                $this->updatedAtAttribute,
                $this->modifyUpdatedAt,
            ],
        ];
        foreach ($attributes as $row) {
            if (is_string($row[0])) {
                // 允许自定义时间日期
                if ($row[1]) {
                    $this->addRules($rules, $row[0]);
                } // 不允许自定义时间日期则暂不需要添加其他规则，系统会根据配置自动为时间日期字段赋值$this->value
                else {
                }
            }
        }

        return ArrayHelper::merge($modelRules, $rules);
    }

    /**
     * 添加规则
     *
     * @param array $rules 规则数据
     * @param string $attribute 规则字段
     */
    protected function addRules(&$rules, $attribute)
    {
        $displayData = $this->getDisplayFormat($attribute, true);
        $saveData = $this->getSaveFormat($attribute, true);
        // 添加`required`规则
        $rules[] = [$attribute, 'required'];
        switch ($this->dateTimeWidget) {
            case '\wocenter\widgets\DateTimePicker':
                // '1901-12-14 04:45:52'的时间戳为 -2147483648，mysql int类型保存的范围从 -2^31 (-2,147,483,648) 到 2^31 – 1 (2,147,483,647) 的整型数据（所有数字）。存储大小为 4 个字节。
                // 添加时间范围限制
                $rules[] = [$attribute, $displayData['type'],
                    'format' => $displayData['format'],
                    'timeZone' => $this->displayTimeZone,
                    'timestampAttribute' => $attribute,
                    'timestampAttributeFormat' => $saveData['format'],
                    'timestampAttributeTimeZone' => $this->saveTimeZone,
                    'min' => -2147483648, 'minString' => '1901-12-14 04:45:52',
                    'max' => time(), 'maxString' => '当前时间',
                ];
                break;
            case '\wocenter\widgets\DateControl':
            case '\kartik\datecontrol\DateControl':
                switch ($saveData['format']) {
                    case 'php:U':
                        $rules[] = [$attribute, '\wocenter\validators\DateControlValidator'];
                        break;
                    default:
                        // 添加时间范围限制
                        $rules[] = [$attribute, $displayData['type'],
                            'format' => $displayData['format'],
                            'timeZone' => $this->displayTimeZone,
                            'timestampAttribute' => $attribute,
                            'timestampAttributeFormat' => $saveData['format'],
                            'timestampAttributeTimeZone' => $this->saveTimeZone,
                            'min' => -2147483648, 'minString' => '1901-12-14 04:45:52',
                            'max' => time(), 'maxString' => '当前时间',
                        ];
                        break;
                }
                break;
            default:
                break;
        }
    }

    /**
     * 创建场景
     * 根据配置自动添加$this->createdAtAttribute和$this->updatedAtAttribute属性字段，
     * 场景里出现的字段会被验证和保存进数据库里
     *
     * @param array $modelScenarios 模型场景
     *
     * @return array
     */
    public function createScenarios(array $modelScenarios)
    {
        switch ($this->owner->getScenario()) {
            case 'default':
                // 默认场景下，只有允许自定义的时间日期字段才会被添加进场景数据里
                foreach ([$this->createdAtAttribute, $this->updatedAtAttribute] as $attribute) {
                    if (
                        is_string($attribute)
                        && !in_array($attribute, $modelScenarios[$this->owner->getScenario()])
                        && (
                            ($attribute == $this->createdAtAttribute && $this->modifyCreatedAt)
                            || ($attribute == $this->updatedAtAttribute && $this->modifyUpdatedAt)
                        )
                    ) {
                        $modelScenarios['default'][] = $attribute;
                    }
                }
                break;
        }

        return $modelScenarios;
    }

    /**
     * 获取指定时间日期属性的显示格式，行为会按照以下序列获取相关值
     *
     * - 1、如果`createdAtDisplayFormat`或`updatedAtDisplayFormat`属性已设置，该值被优先读取
     * - 2、如果`Yii::$app->params`已经配置了键名为`dateControlDisplay`的数据，该值被读取
     * - 3、以上均未设置，则根据[[$createdAtDisplayType]]或[[$updatedAtDisplayType]]属性从[[$_displayFormats]]数组里获取数据
     *
     * @param string $attribute 时间日期属性
     * @param boolean $returnArray 是否返回数组格式的数据，默认为`false`。
     *
     * @return string|array 当$returnArray为`false`时，直接返回显示格式，否则返回数组['format', 'type']
     * @throws InvalidConfigException
     */
    public function getDisplayFormat($attribute, $returnArray = false)
    {
        switch ($attribute) {
            case $this->createdAtAttribute:
                $type = $this->createdAtDisplayType;
                $format = $this->createdAtDisplayFormat;
                break;
            case $this->updatedAtAttribute:
                $type = $this->updatedAtDisplayType;
                $format = $this->updatedAtDisplayFormat;
                break;
            default:
                throw new InvalidConfigException("The property `{$attribute}` does not exists.`");
                break;
        }
        if (empty($format)) {
            if (!empty(Yii::$app->params['dateControlDisplay'][$type])) {
                $format = Yii::$app->params['dateControlDisplay'][$type];
            } else {
                $format = $this->_displayFormats[$type];
            }
        }

        if (strpos($format, 'php:') === false) {
            $format = DateTimeHelper::parseFormat($format, $type);
        }

        return $returnArray ? [
            'format' => $format,
            'type' => $type,
        ] : $format;
    }

    /**
     * 获取指定时间日期属性的保存格式，行为会按照以下序列获取相关值
     *
     * - 1、如果`createdAtSaveFormat`或`updatedAtSaveFormat`属性已设置，该值被优先读取
     * - 2、如果`Yii::$app->params`已经配置了键名为`dateControlSave`的数据，该值被读取
     * - 3、以上均未设置，则根据[[$createdAtDisplayType]]或[[$updatedAtDisplayType]]属性从[[$_saveFormats]]数组里获取数据
     *
     * @param string $attribute 时间日期属性
     * @param boolean $returnArray 是否返回数组格式的数据，默认为`false`。
     *
     * @return string|array 当$returnArray为`false`时，直接返回保存格式，否则返回数组['format', 'type']
     * @throws InvalidConfigException
     */
    public function getSaveFormat($attribute, $returnArray = false)
    {
        switch ($attribute) {
            case $this->createdAtAttribute:
                $type = $this->createdAtDisplayType;
                $format = $this->createdAtSaveFormat;
                break;
            case $this->updatedAtAttribute:
                $type = $this->updatedAtDisplayType;
                $format = $this->updatedAtSaveFormat;
                break;
            default:
                throw new InvalidConfigException("The property `{$attribute}` does not exists.`");
                break;
        }

        if (empty($format)) {
            if (!empty(Yii::$app->params['dateControlSave'][$type])) {
                $format = Yii::$app->params['dateControlSave'][$type];
            } else {
                $format = $this->_saveFormats[$type];
            }
        }

        if (strpos($format, 'php:') === false) {
            $format = DateTimeHelper::parseFormat($format, $type);
        }

        return $returnArray ? [
            'format' => $format,
            'type' => $type,
        ] : $format;
    }

    /**
     * @var string DateControl 小部件会按此时区格式化显示时间日期
     */
    private $_displayTimeZone;

    /**
     * DateControl 小部件会按此时区格式化显示时间日期
     *
     * @return string
     */
    public function getDisplayTimeZone()
    {
        if (empty($this->_displayTimeZone)) {
            if (!empty(Yii::$app->params['dateControlDisplayTimezone'])) {
                $this->_displayTimeZone = Yii::$app->params['dateControlDisplayTimezone'];
            } else {
                $this->_displayTimeZone = Yii::$app->getTimeZone();
            }
        }

        return $this->_displayTimeZone;
    }

    /**
     * @param string $displayTimeZone
     */
    public function setDisplayTimeZone($displayTimeZone)
    {
        $this->_displayTimeZone = $displayTimeZone;
    }

    /**
     * @var string DateControl 小部件会按此时区保存时间日期
     */
    private $_saveTimeZone;

    /**
     * DateControl 小部件会按此时区保存时间日期
     *
     * @return string
     */
    public function getSaveTimeZone()
    {
        if (empty($this->_saveTimeZone)) {
            if (!empty(Yii::$app->params['dateControlSaveTimezone'])) {
                $this->_saveTimeZone = Yii::$app->params['dateControlSaveTimezone'];
            } else {
                $this->_saveTimeZone = Yii::$app->getTimeZone();
            }
        }

        return $this->_saveTimeZone;
    }

    /**
     * @param string $saveTimeZone
     */
    public function setSaveTimeZone($saveTimeZone)
    {
        $this->_saveTimeZone = $saveTimeZone;
    }

}
