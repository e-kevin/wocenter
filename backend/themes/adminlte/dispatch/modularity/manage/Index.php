<?php
namespace wocenter\backend\themes\adminlte\dispatch\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Class Index
 *
 * @package wocenter\backend\themes\adminlte\dispatch\modularity\manage
 */
class Index extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => Wc::$service->getModularity()->getModuleList(),
            'key' => 'id',
            'pagination' => [
                'pageSize' => -1, //不使用分页
            ],
        ]);

        return $this->display('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
