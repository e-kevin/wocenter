<?php
namespace wocenter\backend\themes\adminlte\dispatches\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\modularity\models\Module;
use wocenter\Wc;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Class Index
 *
 * @package wocenter\backend\themes\adminlte\dispatches\modularity\manage
 */
class Index extends Dispatch
{

    /**
     * @param string $app 应用ID
     *
     * @return string|\yii\web\Response
     */
    public function run($app = 'backend')
    {
        $oldAppId = Yii::$app->id;
        Yii::$app->id = $app;

        $dataProvider = new ArrayDataProvider([
            'allModels' => Wc::$service->getModularity()->getModuleList(),
            'key' => 'id',
            'pagination' => [
                'pageSize' => -1, //不使用分页
            ],
        ]);

        Yii::$app->id = $oldAppId;

        return $this->display('index', [
            'dataProvider' => $dataProvider,
            'runModuleList' => (new Module())->getRunModuleList(),
            'app' => $app,
        ]);
    }

}
