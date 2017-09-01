<?php
namespace wocenter\backend\themes\adminlte\dispatches\operate\invite;

use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Clear
 *
 * @package wocenter\backend\themes\adminlte\dispatches\operate\invite
 */
class Clear extends Dispatch
{

    public function run()
    {
        (new Invite())->clearCode();
        $this->success('', self::RELOAD_LIST);
    }

}
