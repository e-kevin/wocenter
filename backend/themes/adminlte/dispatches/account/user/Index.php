<?php
namespace wocenter\backend\themes\adminlte\dispatches\account\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\models\User;
use \wocenter\backend\modules\account\models\UserSearch;
use Yii;

/**
 * Class UserList
 *
 * @package wocenter\backend\themes\adminlte\dispatches\account\user
 */
class Index extends Dispatch
{

    /**
     * @param integer $status
     *
     * @return string|\yii\web\Response
     */
    public function run($status = User::STATUS_ACTIVE)
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams(), ['status' => $status]);
        if ($searchModel->message) {
            $this->error($searchModel->message);
        }

        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ])->display();
    }

}
