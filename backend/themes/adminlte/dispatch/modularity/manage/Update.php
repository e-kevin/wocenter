<?php
namespace wocenter\backend\themes\adminlte\dispatch\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatch\modularity\manage
 */
class Update extends Dispatch
{

    /**
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = Wc::$service->getModularity()->getModuleInfo($id);
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams())) {
                // 是否为系统模块，以模块配置信息为准
                $model->is_system = $model->infoInstance->isSystem ?: $model->is_system;
                if ($model->save(false)) {
                    $this->success($model->message, ["/{$this->controller->module->id}"]);
                } else {
                    $this->error($model->message);
                }
            } else {
                if ($model->getDirtyAttributes()) {
                    $this->error($model->message);
                } else {
                    $this->success($model->message, ["/{$this->controller->module->id}"]);
                }
            }
        }

        return $this->assign([
            'model' => $model,
        ])->display();
    }

}
