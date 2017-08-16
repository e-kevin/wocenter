<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\user;

use wocenter\backend\modules\passport\models\SignupForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Generate
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\user
 */
class Generate extends Dispatch
{

    public function run()
    {
        $model = new SignupForm();
        if ($model->generateUser()) {
            $this->success('生成用户成功', parent::RELOAD_LIST);
        } else {
            $this->error($model->message);
        }
    }

}
