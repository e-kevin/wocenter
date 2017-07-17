<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\actions\MultipleDelete;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\account\models\Tag;
use yii\filters\VerbFilter;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends Controller
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
                'modelClass' => Tag::className(),
            ],
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => Tag::className(),
            ],
        ];
    }

    /**
     * Lists all Tag models.
     *
     * @param $pid
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex($pid = 0)
    {
        return $this->setParams('pid', $pid)->run();
    }

    /**
     * Creates a new Tag model.
     *
     * @param int $pid
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate($pid = 0)
    {
        return $this->setParams('pid', $pid)->run();
    }

    /**
     * Updates an existing Tag model.
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

}
