<?php
namespace wocenter\backend\themes\adminlte\dispatch\action\limit;

use wocenter\backend\modules\action\models\ActionLimitSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Search
 *
 * @package wocenter\backend\themes\adminlte\dispatch\action\limit
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new ActionLimitSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
        ])->display('_search');
    }

}
