<?php
namespace wocenter\widgets;

use kartik\datecontrol\DateControl as baseDateControl;
use wocenter\behaviors\ModifyTimestampBehavior;

/**
 * 初始化[[\kartik\datecontrol\DateControl]]组件的一些基础配置信息
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DateControl extends baseDateControl
{

    public function init()
    {
        $this->initConfigByBehavior();

        parent::init();
    }

    /**
     * 如果存在模型且模型拥有\wocenter\behaviors\ModifyTimestampBehavior行为，则根据行为配置来初始化一些相关参数
     */
    protected function initConfigByBehavior()
    {
        if ($this->hasModel() && $this->model->hasProperty('dateTimeWidget')) {
            /** @var ModifyTimestampBehavior $modifyTimestampBehavior */
            $modifyTimestampBehavior = $this->model;
            $displayData = $modifyTimestampBehavior->getDisplayFormat($this->attribute, true);
            $saveData = $modifyTimestampBehavior->getSaveFormat($this->attribute, true);
            $this->type = $displayData['type'];
            $this->displayFormat = $displayData['format'];
            $this->saveFormat = $saveData['format'];
            $this->displayTimezone = $modifyTimestampBehavior->displayTimeZone;
            $this->saveTimezone = $modifyTimestampBehavior->saveTimeZone;
        }
    }

}
