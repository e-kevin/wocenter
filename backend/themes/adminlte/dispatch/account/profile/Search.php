<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\profile;

use wocenter\backend\modules\account\models\ExtendProfileSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Search
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\profile
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new ExtendProfileSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
        ])->display('_search');
    }

}
