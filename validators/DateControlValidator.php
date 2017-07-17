<?php
namespace wocenter\validators;

use Yii;
use yii\helpers\StringHelper;
use yii\validators\NumberValidator;
use yii\web\JsExpression;

/**
 * DateControl 小部件的验证类，主要是对时间戳的时间日期范围验证
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DateControlValidator extends NumberValidator
{
    /**
     * @var integer 日期上限，默认为`null`，表示没有上限。当为整数时，表示该值是一个时间戳。
     */
    public $max;
    /**
     * @var integer 日期下限，默认为`null`，表示没有下限。当为整数时，表示该值是一个时间戳。
     *
     * '1901-12-14 04:45:52'的时间戳为 -2147483648，mysql int类型保存的范围从 -2^31 (-2,147,483,648) 到 2^31 – 1 (2,147,483,647)
     *     的整型数据（所有数字）。存储大小为 4 个字节。
     */
    public $min = -2147483648;
    /**
     * @var string 当被验证的值大于[[max]]时，将会显示该条用户定义的错误信息
     */
    public $tooBig;
    /**
     * @var string 当被验证的值大于[[min]]时，将会显示该条用户定义的错误信息
     */
    public $tooSmall;
    /**
     * @var string 当错误信息显示时，该值将被用于显示错误的上限信息
     * 当该值为`null`时，将被赋予[[max]]值
     */
    public $maxString;
    /**
     * @var string 当错误信息显示时，该值将被用于显示错误的下限信息
     * 当该值为`null`时，将被赋予[[min]]值
     */
    public $minString = '1901-12-14 04:45:52';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->integerOnly = true; // 时间戳必须是整数型
        if ($this->maxString === null) {
            $this->maxString = (string)$this->max;
        }
        if ($this->minString === null) {
            $this->minString = (string)$this->min;
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $timestamp = $model->$attribute;
        if (is_array($timestamp) || (is_object($timestamp) && !method_exists($timestamp, '__toString'))) {
            $this->addError($model, $attribute, $this->message);

            return;
        } elseif (!preg_match($this->integerPattern, StringHelper::normalizeNumber($timestamp))) {
            $this->addError($model, $attribute, $this->message);
        } elseif ($this->min !== null && $timestamp < $this->min) {
            $this->addError($model, $attribute, $this->tooSmall, ['min' => $this->minString]);
        } elseif ($this->max !== null && $timestamp > $this->max) {
            $this->addError($model, $attribute, $this->tooBig, ['max' => $this->maxString]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return [Yii::t('yii', '{attribute} is invalid.'), []];
        }
        if (!preg_match($this->integerPattern, StringHelper::normalizeNumber($value))) {
            return [$this->message, []];
        } elseif ($this->min !== null && $value < $this->min) {
            return [$this->tooSmall, ['min' => $this->minString]];
        } elseif ($this->max !== null && $value > $this->max) {
            return [$this->tooBig, ['max' => $this->maxString]];
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getClientOptions($model, $attribute)
    {
        $label = $model->getAttributeLabel($attribute);

        $options = [
            'pattern' => new JsExpression($this->integerOnly ? $this->integerPattern : $this->numberPattern),
            'message' => Yii::$app->getI18n()->format($this->message, [
                'attribute' => $label,
            ], Yii::$app->language),
        ];

        if ($this->min !== null) {
            // ensure numeric value to make javascript comparison equal to PHP comparison
            // https://github.com/yiisoft/yii2/issues/3118
            $options['min'] = is_string($this->min) ? (float)$this->min : $this->min;
            $options['tooSmall'] = Yii::$app->getI18n()->format($this->tooSmall, [
                'attribute' => $label,
                'min' => $this->minString,
            ], Yii::$app->language);
        }
        if ($this->max !== null) {
            // ensure numeric value to make javascript comparison equal to PHP comparison
            // https://github.com/yiisoft/yii2/issues/3118
            $options['max'] = is_string($this->max) ? (float)$this->max : $this->max;
            $options['tooBig'] = Yii::$app->getI18n()->format($this->tooBig, [
                'attribute' => $label,
                'max' => $this->maxString,
            ], Yii::$app->language);
        }
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }

}
