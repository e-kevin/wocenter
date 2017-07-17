<?php
namespace wocenter\backend\modules\system\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\system\models\Config;
use yii\filters\VerbFilter;

class ConfigManagerController extends Controller
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => Config::className(),
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->runDispatch();
    }

    /**
     * 搜索操作
     *
     * @return mixed
     */
    public function actionSearch()
    {
        return $this->runDispatch();
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionView($id)
    {
        return $this->setParams('id', $id)->run();
    }

    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * @param $id
     *
     * @return array|string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

}
