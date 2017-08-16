<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\identity;

use wocenter\backend\modules\account\models\IdentityGroup;
use wocenter\backend\modules\account\models\IdentitySearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Search
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\identity
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new IdentitySearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'identityGroup' => (new IdentityGroup())->getSelectList(),
        ])->display('_search');
    }

}
