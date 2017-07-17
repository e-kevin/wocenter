<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\core\Controller;
use Yii;
use yii\filters\VerbFilter;

class AdminController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'relieve' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 管理员列表
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->runDispatch();
    }

    /**
     * 解除管理员
     *
     * @return mixed
     */
    public function actionRelieve()
    {
        return $this->setParams('id', Yii::$app->getRequest()->getBodyParam('selection'))->run();
    }

    /**
     * 添加管理员
     *
     * @return mixed
     */
    public function actionAdd()
    {
        return $this->runDispatch();
    }

    /**
     * 更新管理员
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

}
