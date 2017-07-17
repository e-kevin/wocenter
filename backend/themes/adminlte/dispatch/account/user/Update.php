<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\models\User;
use wocenter\traits\LoadModelTrait;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\user
 */
class Update extends Dispatch
{

    use LoadModelTrait;

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run()
    {
        /** @var User $model */
        $model = $this->loadModel(User::className(), $this->_params['id'], true, [
            'scenario' => 'update',
        ]);
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->module->id}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign('model', $model)->display();
    }

}