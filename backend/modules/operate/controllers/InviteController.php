<?php
namespace wocenter\backend\modules\operate\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\actions\MultipleDelete;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\operate\models\Invite;
use yii\filters\VerbFilter;

/**
 * InviteController implements the CRUD actions for Invite model.
 */
class InviteController extends Controller
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
                    'clear' => ['post'],
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
                'modelClass' => Invite::className(),
                'markAsDeleted' => true,
                'deletedMarkAttribute' => 'status',
                'deletedMarkValue' => Invite::CODE_DELETED
            ],
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => Invite::className(),
                'markAsDeleted' => true,
                'deletedMarkAttribute' => 'status',
                'deletedMarkValue' => Invite::CODE_DELETED
            ],
        ];
    }

    /**
     * Lists all Invite models.
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

    public function actionGenerate()
    {
        return $this->runDispatch();
    }

    public function actionClear()
    {
        return $this->runDispatch();
    }

}
