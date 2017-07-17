<?php
namespace wocenter\backend\modules\log\controllers;

use wocenter\backend\core\Controller;

/**
 * ScoreController implements the CRUD actions for UserScoreLog model.
 */
class ScoreController extends Controller
{

    /**
     * Lists all UserScoreLog models.
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
