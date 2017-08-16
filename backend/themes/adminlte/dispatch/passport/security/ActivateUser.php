<?php
namespace wocenter\backend\themes\adminlte\dispatch\passport\security;

use wocenter\backend\modules\passport\models\SecurityForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class ActivateUser
 *
 * @package wocenter\backend\themes\adminlte\dispatch\passport\security
 */
class ActivateUser extends Dispatch
{

    /**
     * @return \yii\web\Response
     */
    public function run()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->controller->goHome();
        }

        $model = new SecurityForm();
        $code = Yii::$app->getRequest()->getQueryParam('code', 0);
        if ($model->activateUser($code)) {
            $this->success('帐号已成功激活', Yii::$app->getUser()->loginUrl);
        } else {
            $this->error($model->message, 'activate-account', 2);
        }
    }

}
