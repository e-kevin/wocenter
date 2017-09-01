<?php
namespace wocenter\backend\themes\adminlte\dispatches\action\limit;

use wocenter\backend\modules\action\models\ActionLimitSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Index
 *
 * @package wocenter\backend\themes\adminlte\dispatches\action\limit
 */
class Index extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new ActionLimitSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }

        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ])->display();
    }

}
