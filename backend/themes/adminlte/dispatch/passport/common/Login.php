<?php
namespace wocenter\backend\themes\adminlte\dispatch\passport\common;

use wocenter\backend\modules\passport\events\validateAdministrator;
use wocenter\backend\modules\passport\models\LoginForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\helpers\UrlHelper;
use Yii;

/**
 * Class Login
 *
 * @package wocenter\backend\themes\adminlte\dispatch\passport\common
 */
class Login extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (Yii::$app->getSession()->has(LoginForm::TMP_LOGIN)) {
            return $this->controller->redirect('step');
        }

        $request = Yii::$app->getRequest();
        $refererUrl = $request->get(Yii::$app->params['redirect']);
        $message = '登录成功。';
        if ($refererUrl) {
            $refererUrl = UrlHelper::unsetParams(base64_decode($refererUrl), '_pjax,reload-list'); // 剔除不必要参数
            $message .= '正在为您跳转至登录前的页面';
        }
        // 已经登录，如果检测到URL地址里有跳转链接，则直接跳转至链接地址，否则返回首页
        if (!Yii::$app->getUser()->getIsGuest()) {
            if ($refererUrl) {
                return $this->controller->redirect($refererUrl);
            } else {
                return $this->controller->goHome();
            }
        }

        $model = new LoginForm();
        // 绑定事件
        $model->on(LoginForm::EVENT_BEFORE_LOGIN, [new validateAdministrator(), 'run']);

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->login()) {
                $this->success($message, $refererUrl ?: Yii::$app->getUser()->getReturnUrl());
            } else {
                if ($model->message === LoginForm::NEED_STEP) {
                    $this->success('正在为您跳转至步骤页面~', 'step');
                } else {
                    $this->error($model->message);
                }
            }
        } else {
            return $this->assign([
                'model' => $model,
                'returnUrl' => $refererUrl,
            ])->display();
        }
    }

}