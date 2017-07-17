<?php
namespace wocenter\backend\modules\data\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\data\models\AreaRegion;
use yii\filters\VerbFilter;

/**
 * AreaRegionController implements the CRUD actions for AreaRegion model.
 */
class AreaRegionController extends Controller
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
                'modelClass' => AreaRegion::className(),
            ],
        ];
    }

    /**
     * Lists all AreaRegion models.
     *
     * @param int $pid
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex($pid = 0)
    {
        return $this->setParams('pid', $pid)->run();
    }

    /**
     * @param int $pid
     *
     * @return string|\yii\web\Response
     */
    public function actionSearch($pid = 0)
    {
        return $this->setParams('pid', $pid)->run();
    }

    /**
     * Creates a new AreaRegion model.
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
     * Updates an existing AreaRegion model.
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
