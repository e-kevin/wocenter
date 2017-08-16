<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\admin;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\models\BackendUserSearch;
use Yii;

/**
 * Class Index
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\admin
 */
class Index extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new BackendUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        if ($searchModel->message) {
            $this->error($searchModel->message);
        }

        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ])->display();
    }

}
