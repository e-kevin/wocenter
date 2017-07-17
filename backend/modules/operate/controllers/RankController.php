<?php
namespace wocenter\backend\modules\operate\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\operate\models\Rank;
use yii\filters\VerbFilter;

/**
 * RankController implements the CRUD actions for Rank model.
 */
class RankController extends Controller
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
                'modelClass' => Rank::className(),
            ],
        ];
    }

    /**
     * Lists all Rank models.
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->runDispatch();
    }

    /**
     * Creates a new Rank model.
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * Updates an existing Rank model.
     *
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
