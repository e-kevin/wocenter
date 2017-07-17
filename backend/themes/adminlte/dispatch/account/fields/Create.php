<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\fields;

use wocenter\backend\modules\account\models\ExtendFieldSetting;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\fields
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new ExtendFieldSetting();
        $model->loadDefaultValues();
        $model->profile_id = (int)$this->_params['profile_id'];
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, [
                    "/{$this->controller->getUniqueId()}",
                    'profile_id' => $model->profile_id,
                ]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
            'profileName' => ExtendProfile::find()->where('id = :id', [
                ':id' => $model->profile_id,
            ])->select('profile_name')->scalar(),
            'formTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
        ])->display();
    }

}