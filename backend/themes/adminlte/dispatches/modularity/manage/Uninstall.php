<?php
namespace wocenter\backend\themes\adminlte\dispatches\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatches\modularity\manage
 */
class Uninstall extends Dispatch
{

    /**
     * @param string $id
     * @param string $app 应用ID
     *
     * @throws \Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id, $app = 'backend')
    {
        $oldAppId = Yii::$app->id;
        Yii::$app->id = $app;

        $model = Wc::$service->getModularity()->getModuleInfo($id);

        Yii::$app->id = $oldAppId;
        if ($model->infoInstance->canUninstall) {
            // 调用模块内置卸载方法
            if (!$model->infoInstance->uninstall()) {
                $this->error(Wc::getErrorMessage());
            }
            if ($model->delete()) {
                $this->success('卸载成功', parent::RELOAD_PAGE);
            } else {
                $this->error('卸载失败');
            }
        } else {
            $this->error($id . ' 模块属于系统模块，暂不支持卸载');
        }
    }

}
