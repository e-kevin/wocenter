<?php
namespace wocenter\backend\modules\log\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\actions\MultipleDelete;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\log\models\ActionLog;
use yii\filters\VerbFilter;

/**
 * ActionLogController implements the CRUD actions for ActionLog model.
 */
class ActionController extends Controller
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
                    'batch-delete' => ['post'],
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
            'batch-delete' => [
                'class' => MultipleDelete::className(),
                'modelClass' => ActionLog::className(),
            ],
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => ActionLog::className(),
            ],
        ];
    }

    /**
     * Lists all ActionLog models.
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

}
