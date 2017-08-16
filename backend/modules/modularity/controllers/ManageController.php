<?php
namespace wocenter\backend\modules\modularity\controllers;

use wocenter\backend\core\Controller;
use wocenter\core\Dispatch;
use wocenter\Wc;
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
        ];
    }

    /**
     * 清理缓存
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionClearCache()
    {
        Wc::$service->getModularity()->clearCache();

        return $this->success('清理成功', Dispatch::RELOAD_LIST);
    }

}
