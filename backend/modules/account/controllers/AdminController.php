<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\core\Controller;
use Yii;
use yii\filters\VerbFilter;

class AdminController extends Controller
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
                    'relieve' => ['post'],
                ],
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
            'relieve',
            'add',
            'update',
        ];
    }

}
