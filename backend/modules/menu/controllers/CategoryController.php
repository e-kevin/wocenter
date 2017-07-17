<?php
namespace wocenter\backend\modules\menu\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\menu\models\MenuCategory;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for MenuCategory model.
 */
class CategoryController extends Controller
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
                    'sync-menus' => ['post'],
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
                'modelClass' => MenuCategory::className(),
            ],
        ];
    }

    /**
     * Lists all MenuCategory models.
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->runDispatch();
    }

    /**
     * Creates a new MenuCategory model.
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * Updates an existing MenuCategory model.
     *
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

    /**
     * 同步菜单
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionSyncMenus()
    {
        return $this->runDispatch();
    }

}
