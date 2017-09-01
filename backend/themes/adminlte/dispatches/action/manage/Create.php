<?php
namespace wocenter\backend\themes\adminlte\dispatches\action\manage;

use wocenter\backend\modules\action\models\Action;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatches\action\manage
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $model = new Action();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->module->id}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
            'installedModuleSelectList' => Wc::$service->getModularity()->getInstalledModuleSelectList()
        ])->display();
    }

}