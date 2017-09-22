<?php
namespace wocenter\frontend\themes\basic\dispatches\frontend\site;

use \wocenter\core\Dispatch;

/**
 * 关于
 */
class About extends Dispatch
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->display();
    }

}
