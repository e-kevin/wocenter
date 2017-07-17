<?php
namespace wocenter\backend\modules\operate\controllers;

use wocenter\backend\core\Controller;

/**
 * InviteUserInfoController implements the CRUD actions for InviteUserInfo model.
 */
class InviteUserInfoController extends Controller
{

    /**
     * Lists all InviteUserInfo models.
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->runDispatch();
    }

    public function actionSearch()
    {
        return $this->runDispatch();
    }

    /**
     * Creates a new InviteUserInfo model.
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        return $this->runDispatch();
    }

    /**
     * Updates an existing InviteUserInfo model.
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
