<?php
namespace wocenter\backend\themes\adminlte\dispatch\operate\inviteUserInfo;

use wocenter\backend\modules\operate\models\InviteUserInfo;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatch\operate\inviteUserInfo
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new InviteUserInfo();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"], 2);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
        ])->display();
    }

}
