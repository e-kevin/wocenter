<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\account\models\ExtendProfile;
use yii\filters\VerbFilter;

/**
 * ProfileController implements the CRUD actions for FieldGroup model.
 */
class ProfileController extends Controller
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
                'modelClass' => ExtendProfile::className(),
            ],
        ];
    }

    /**
     * Lists all FieldGroup models.
     *
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
     * Creates a new FieldGroup model.
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * Updates an existing FieldGroup model.
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
