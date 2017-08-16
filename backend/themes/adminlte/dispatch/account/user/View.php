<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\models\User;
use wocenter\traits\LoadModelTrait;
use Yii;

/**
 * Class View
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\user
 */
class View extends Dispatch
{

    use LoadModelTrait;

    /**
     * @param integer $id
     *
     * @return string|\yii\web\Response
     */
    public function run($id)
    {
        return $this->assign([
            'model' => $this->loadModel(User::className(), $id),
        ])->display();
    }

}
