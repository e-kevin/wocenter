<?php
namespace wocenter\backend\themes\adminlte\dispatch\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class ClearCache
 *
 * @package wocenter\backend\themes\adminlte\dispatch\modularity\manage
 */
class ClearCache extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        Wc::$service->getModularity()->clearCache();

        return $this->success('清理成功', Dispatch::RELOAD_LIST);
    }

}
