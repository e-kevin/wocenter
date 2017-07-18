<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\admin;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\models\BackendUser;
use Yii;

/**
 * Class Add
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\admin
 */
class Add extends Dispatch
{

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