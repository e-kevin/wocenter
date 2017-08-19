<?php
namespace wocenter\backend\modules\modularity\controllers;

use wocenter\backend\core\Controller;
use yii\filters\VerbFilter;

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
                    'uninstall' => ['post'],
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
            'update',
            'install',
            'uninstall',
            'clear-cache',
        ];
    }

}
