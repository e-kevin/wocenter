<?php
namespace wocenter\backend\modules\action\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\action\models\ActionLimit;
use yii\filters\VerbFilter;

/**
 * ActionLimitController implements the CRUD actions for ActionLimit model.
 */
class LimitController extends Controller
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
                'modelClass' => ActionLimit::className(),
            ],
        ];
    }

    /**
     * Lists all ActionLimit models.
     *
     * @return mixed
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
     * Creates a new ActionLimit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * Updates an existing ActionLimit model.
     * If update is successful, the browser will be redirected to the 'view' page.
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
