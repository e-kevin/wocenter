<?php
namespace wocenter\backend\themes\adminlte\dispatches\backend\site;

use wocenter\backend\themes\adminlte\components\Dispatch;

/**
 * 后台首页
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
