<?php
namespace wocenter\backend\themes\adminlte\dispatch\operate\invite;

use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Clear
 *
 * @package wocenter\backend\themes\adminlte\dispatch\operate\invite
 */
class Clear extends Dispatch
{

    public function run()
    {
        if (Yii::$app->getRequest()->getIsPost()) {
            (new Invite())->clearCode();
            $this->success('', self::RELOAD_LIST);
        }
    }

}