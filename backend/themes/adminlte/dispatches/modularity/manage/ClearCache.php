<?php
namespace wocenter\backend\themes\adminlte\dispatches\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class ClearCache
 *
 * @package wocenter\backend\themes\adminlte\dispatches\modularity\manage
 */
class ClearCache extends Dispatch
{

    /**
     * @param string $app 应用ID
     *
     * @return string|\yii\web\Response
     */
    public function run($app = 'backend')
    {
        $oldAppId = Yii::$app->id;
        Yii::$app->id = $app;

        Wc::$service->getModularity()->clearCache();

        Yii::$app->id = $oldAppId;

        return $this->success('清理成功', Dispatch::RELOAD_PAGE);
    }

}
