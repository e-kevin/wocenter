<?php
namespace wocenter\backend\themes\adminlte\dispatch\action\manage;

use wocenter\backend\modules\action\models\ActionSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Search
 *
 * @package wocenter\backend\themes\adminlte\dispatch\action\manage
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new ActionSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'installedModuleSelectList' => Wc::$service->getModularity()->getInstalledModuleSelectList(),
        ])->display('_search');
    }

}
