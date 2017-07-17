<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\account\models\Identity;
use yii\filters\VerbFilter;

/**
 * IdentityController implements the CRUD actions for Identity model.
 */
class IdentityController extends Controller
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
                'modelClass' => Identity::className(),
            ],
        ];
    }

    /**
     * Lists all Identity models.
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
     * Creates a new Identity model.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * Updates an existing Identity model.
     *
     * @param $id
     *
     * @return array|string
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

    /**
     * 默认信息配置
     *
     * @param string $type 配置类型 [score, avatar, rank, tag, profile, signup]
     * @param integer $id 身份ID
     *
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSetting($type, $id)
    {
        return $this->setParams([
            'type' => $type,
            'id' => $id,
        ])->run();
    }

}
