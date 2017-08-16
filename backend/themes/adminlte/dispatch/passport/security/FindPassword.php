<?php
namespace wocenter\backend\themes\adminlte\dispatch\passport\security;

use wocenter\backend\modules\passport\models\SecurityForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class FindPassword
 *
 * @package wocenter\backend\themes\adminlte\dispatch\passport\security
 */
class FindPassword extends Dispatch
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

        $model = new SecurityForm(['scenario' => SecurityForm::SCENARIO_FIND_PASSWORD]);
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->findPassword()) {
                return $this->controller->redirect('find-password-successful');
            } else {
                $this->error($model->message);
            }
        } else {
            return $this->assign('model', $model)->display();
        }
    }

}
