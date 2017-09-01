<?php
namespace wocenter\backend\themes\adminlte\dispatches\notification\setting;

use wocenter\backend\modules\notification\models\Notify;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatches\notification\setting
 */
class Update extends Dispatch
{

    use LoadModelTrait;

    /**
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var Notify $model */
        $model = $this->loadModel(Notify::className(), $id);
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
