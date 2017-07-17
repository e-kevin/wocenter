<?php
namespace wocenter\backend\themes\adminlte\dispatch\data\areaRegion;

use wocenter\backend\modules\data\models\AreaRegionSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Index
 *
 * @package wocenter\backend\themes\adminlte\dispatch\data\areaRegion
 */
class Index extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new AreaRegionSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }

        $pid = $this->_params[$searchModel->breadcrumbParentParam];
        $breadcrumbs = $searchModel->getBreadcrumbs($pid, '区域管理', '/data/area-region');

        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pid' => $pid,
            'breadcrumbs' => $breadcrumbs,
            'title' => $breadcrumbs[$pid]['label'],
        ])->display();
    }

}