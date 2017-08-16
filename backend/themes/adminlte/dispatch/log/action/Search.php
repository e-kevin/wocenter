<?php
namespace wocenter\backend\themes\adminlte\dispatch\log\action;

use wocenter\backend\modules\action\models\Action;
use wocenter\backend\modules\log\models\ActionLogSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\libs\Constants;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Search
 *
 * @package wocenter\backend\themes\adminlte\dispatch\log\action
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new ActionLogSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        $actionModel = new Action();

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'actionSelectList' => $actionModel->getSelectList(),
            'actionTypeList' => ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], $actionModel->typeList),
        ])->display('_search');
    }

}
