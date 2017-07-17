<?php
namespace wocenter\backend\modules\menu\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\menu\models\Menu;
use yii\filters\VerbFilter;

/**
 * DetailController implements the CRUD actions for Menu model.
 */
class DetailController extends Controller
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
                'modelClass' => Menu::className(),
            ],
        ];
    }

    /**
     * Lists all Menu models.
     *
     * @param string|integer $category
     * @param integer $pid
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex($category = '', $pid = 0)
    {
        return $this->setParams([
            'category' => $category,
            'pid' => $pid
        ])->run();
    }

    /**
     * Creates a new Menu model.
     *
     * @param $category
     * @param integer $pid
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate($category = '', $pid = 0)
    {
        return $this->setParams([
            'category' => $category,
            'pid' => $pid
        ])->run();
    }

    /**
     * Updates an existing Menu model.
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        return $this->setParams([
            'id' => $id,
        ])->run();
    }

}
