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
     * 模块列表
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->runDispatch();
    }

    /**
     * 更新模块
     *
     * @author E-Kevin <e-kevin@qq.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

    /**
     * 安装模块
     *
     * @author E-Kevin <e-kevin@qq.com>
     *
     * @param $id
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionInstall($id)
    {
        return $this->setParams('id', $id)->run();
    }

    /**
     * 卸载模块
     *
     * @author E-Kevin <e-kevin@qq.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function actionUninstall($id)
    {
        return $this->setParams('id', $id)->run();
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
