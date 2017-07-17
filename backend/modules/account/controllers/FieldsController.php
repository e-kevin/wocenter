<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\account\models\ExtendFieldSetting;
use yii\filters\VerbFilter;

/**
 * FieldsController implements the CRUD actions for FieldSetting model.
 */
class FieldsController extends Controller
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
                'modelClass' => ExtendFieldSetting::className(),
            ],
        ];
    }

    /**
     * Lists all FieldSetting models.
     *
     * @param integer $profile_id
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex($profile_id = 0)
    {
        return $this->setParams('profile_id', $profile_id)->run();
    }

    /**
     * Creates a new FieldSetting model.
     *
     * @param integer $profile_id 扩展档案ID
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate($profile_id)
    {
        return $this->setParams('profile_id', $profile_id)->run();
    }

    /**
     * Updates an existing FieldSetting model.
     *
     * @param integer $id
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
