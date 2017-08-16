<?php
namespace wocenter\backend\themes\adminlte\dispatch\passport\security;

use wocenter\backend\modules\passport\models\SecurityForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class ResetPassword
 *
 * @package wocenter\backend\themes\adminlte\dispatch\passport\security
 */
class ResetPassword extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->controller->goHome();
        }

        $request = Yii::$app->getRequest();
        // 重置密码令牌
        $token = $request->getQueryParam('token', '');
        if (empty($token)) {
            $this->error('重置密码链接已失效，请重新找回', 'find-password');
        }

        $model = new SecurityForm(['scenario' => SecurityForm::SCENARIO_RESET_PASSWORD]);
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->resetPasswordByToken($token)) {
                $this->success('密码重置成功，正在为您跳转至登录页面', Yii::$app->getUser()->loginUrl, 2);
            } else {
                $this->error($model->message, 'find-password');
            }
        } else {
            return $this->assign('model', $model)->display();
        }
    }

}
