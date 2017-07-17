<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\admin;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\models\BackendUser;
use Yii;

/**
 * Class Relieve
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\admin
 */
class Relieve extends Dispatch
{

    public function run()
    {
        $model = new BackendUser();
        if ($model->relieve($this->_params['id'])) {
            $this->success('解除管理员成功', parent::RELOAD_LIST);
        } else {
            $this->error($model->message);
        }
    }

}