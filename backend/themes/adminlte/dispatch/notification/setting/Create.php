<?php
namespace wocenter\backend\themes\adminlte\dispatch\notification\setting;

use wocenter\backend\modules\notification\models\Notify;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatch\notification\setting
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $model = new Notify();
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
        ])->display();
    }

}
