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
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run()
    {
        return $this->assign([
            'model' => $this->loadModel(User::className(), $this->_params['id']),
        ])->display();
    }

}