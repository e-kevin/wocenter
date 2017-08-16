<?php
namespace wocenter\backend\modules\action\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\action\models\Action;
use yii\filters\VerbFilter;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class ManageController extends Controller
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
                'modelClass' => Action::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            'index',
            'search',
            'create',
            'update',
        ];
    }

}
