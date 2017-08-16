<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\user;

use wocenter\backend\modules\passport\models\SecurityForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class InitPassword
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\user
 */
class InitPassword extends Dispatch
{

    public function run()
    {
        $selections = Yii::$app->getRequest()->getBodyParam('selection');
        $model = new SecurityForm();
        if ($model->initPassword($selections)) {
            $this->success($model->message, '', 3);
        } else {
            $this->error($model->message);
        }
    }

}
