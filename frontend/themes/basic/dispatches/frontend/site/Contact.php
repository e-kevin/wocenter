<?php
namespace wocenter\frontend\themes\basic\dispatches\frontend\site;

use \wocenter\core\Dispatch;

/**
 * 联系
 */
class Contact extends Dispatch
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->display();
    }

}
