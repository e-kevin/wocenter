<?php
namespace wocenter\frontend\themes\basic\dispatches\frontend\site;

use \wocenter\core\Dispatch;

/**
 * 前台首页
 */
class Index extends Dispatch
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->display();
    }

}
