<?php
namespace wocenter\backend\core;

use wocenter\core\View as baseView;
use wocenter\Wc;
use Yii;

/**
 * 后台基础View类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class View extends baseView
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->themeName = Wc::$service->getSystem()->getConfig()->get('BACKEND_THEME', 'basic');

        parent::init();
    }

}
