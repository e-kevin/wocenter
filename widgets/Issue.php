<?php

namespace wocenter\widgets;

use wocenter\interfaces\RunningExtensionInterface;
use wocenter\Wc;
use Yii;
use yii\base\Widget;

/**
 * 反馈信息视图小部件，方便用户向开发者提交bug等信息
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Issue extends Widget
{
    
    public $context;

    public function init()
    {
        parent::init();
    
        /** @var RunningExtensionInterface $runningExtension */
        $runningExtension = Wc::getRunningExtension($this->context ?? Yii::$app->controller);
        $issueUrl = $runningExtension->getInfo()->getIssueUrl() ?: $runningExtension->defaultExtension()->getInfo()->getIssueUrl();

        echo $this->render('@wocenter/views/_issue-message', [
            'issueUrl' => $issueUrl,
        ]);
    }

}
