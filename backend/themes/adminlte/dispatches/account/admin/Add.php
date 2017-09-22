<?php
namespace wocenter\backend\themes\adminlte\dispatches\account\admin;

use wocenter\backend\modules\account\models\BackendUser;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Add
 *
 * @package wocenter\backend\themes\adminlte\dispatches\account\admin
 */
class Add extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new BackendUser();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success('添加管理员成功', ["/{$this->controller->getUniqueId()}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
            'showStatus' => true,
        ])->display();
    }

}
