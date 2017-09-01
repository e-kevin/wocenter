<?php
namespace wocenter\backend\themes\adminlte\dispatches\account\profile;

use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatches\account\profile
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $model = new ExtendProfile();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
        ])->display();
    }

}
