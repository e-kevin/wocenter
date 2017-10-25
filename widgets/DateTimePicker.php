<?php

namespace wocenter\widgets;

use kartik\datetime\DateTimePicker as kvDateTimePicker;
use yii\helpers\ArrayHelper;

/**
 * 主要是对[[\kartik\datetime\DateTimePicker]]组件的配置参数做说明
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DateTimePicker extends kvDateTimePicker
{
    
    /**
     * The date format, combination of p, P, h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy.
     * - p : meridian in lower case ('am' or 'pm') - according to locale file
     * - P : meridian in upper case ('AM' or 'PM') - according to locale file
     * - s : seconds without leading zeros
     * - ss : seconds, 2 digits with leading zeros
     * - i : minutes without leading zeros
     * - ii : minutes, 2 digits with leading zeros
     * - h : hour without leading zeros - 24-hour format
     * - hh : hour, 2 digits with leading zeros - 24-hour format
     * - H : hour without leading zeros - 12-hour format
     * - HH : hour, 2 digits with leading zeros - 12-hour format
     * - d : day of the month without leading zeros
     * - dd : day of the month, 2 digits with leading zeros
     * - m : numeric representation of month without leading zeros
     * - mm : numeric representation of the month, 2 digits with leading zeros
     * - M : short textual representation of a month, three letters
     * - MM : full textual representation of a month, such as January or March
     * - yy : two digit representation of a year
     *
     * @var string
     */
    public $format = 'Y-m-d H:i:s';
    
    /**
     * weekStart
     * Day of the week start. 0 (Sunday) to 6 (Saturday)
     *
     * @var integer
     */
    public $weekStart = 0;
    
    /**
     * The earliest date that may be selected; all earlier dates will be disabled.
     * '1901-12-14 04:45:52'的时间戳为 -2147483648，mysql int类型保存的范围从 -2^31 (-2,147,483,648) 到 2^31 – 1 (2,147,483,647)
     * 的整型数据（所有数字）。存储大小为 4 个字节。
     *
     * @var string
     */
    public $startDate = '1901-12-14 04:45:52';
    
    /**
     * The latest date that may be selected; all later dates will be disabled.
     *
     * @var string
     */
    public $endDate = null;
    
    /**
     * Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday). Multiple values should be
     * comma-separated. Example: disable weekends: '0,6' or [0,6].
     *
     * @var string|array
     */
    public $daysOfWeekDisabled = [];
    
    /**
     * Whether or not to close the datetimepicker immediately when a date is selected.
     *
     * @var boolean
     */
    public $autoclose = true;
    
    /**
     * The view that the datetimepicker should show when it is opened. Accepts values of :
     * - 0 or 'hour' for the hour view
     * - 1 or 'day' for the day view
     * - 2 or 'month' for month view (the default)
     * - 3 or 'year' for the 12-month overview
     * - 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
     *
     * @var string|integer
     */
    public $startView = 2;
    
    /**
     * The lowest view that the datetimepicker should show.
     * - 0 or 'hour' for the hour view
     * - 1 or 'day' for the day view
     * - 2 or 'month' for month view (the default)
     * - 3 or 'year' for the 12-month overview
     * - 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
     *
     * @var string|integer
     */
    public $minView = 0;
    
    /**
     * The highest view that the datetimepicker should show.
     * - 0 or 'hour' for the hour view
     * - 1 or 'day' for the day view
     * - 2 or 'month' for month view (the default)
     * - 3 or 'year' for the 12-month overview
     * - 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
     *
     * @var string|integer
     */
    public $maxView = 4;
    
    /**
     * If true or "linked", displays a "Today" button at the bottom of the datetimepicker to select the current date.
     * If true, the "Today" button will only move the current date into view;
     * If "linked", the current date will also be selected.
     *
     * @var boolean|string
     */
    public $todayBtn = true;
    
    /**
     * If true, highlights the current date.
     *
     * @var boolean
     */
    public $todayHighlight = true;
    
    /**
     * Whether or not to allow date navigation by arrow keys.
     *
     * @var boolean
     */
    public $keyboardNavigation = true;
    
    /**
     * Whether or not to force parsing of the input value when the picker is closed.
     * That is, when an invalid date is left in the input field by the user, the picker will forcibly parse that value,
     * and set the input's value to the new, valid date, conforming to the given format.
     *
     * @var boolean
     */
    public $forceParse = true;
    
    public $showMeridian = false;
    
    /**
     * The increment used to build the hour view. A preset is created for each minuteStep minutes.
     *
     * @var integer
     */
    public $minuteStep = 5;
    
    public $pluginOptions = [];
    
    public function init()
    {
        $this->pluginOptions = ArrayHelper::merge([
            'format' => $this->convertDateFormat($this->format),
            'weekStart' => $this->weekStart,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'daysOfWeekDisabled' => $this->daysOfWeekDisabled,
            'autoclose' => $this->autoclose,
            'startView' => $this->startView,
            'minView' => $this->minView,
            'maxView' => $this->maxView,
            'todayBtn' => $this->todayBtn,
            'todayHighlight' => $this->todayHighlight,
            'keyboardNavigation' => $this->keyboardNavigation,
            'forceParse' => $this->forceParse,
            'showMeridian' => $this->showMeridian,
            'minuteStep' => $this->minuteStep,
        ], $this->pluginOptions);
        
        parent::init();
    }
    
}
