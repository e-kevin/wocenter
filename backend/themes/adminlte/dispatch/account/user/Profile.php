<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Profile
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\user
 */
class Profile extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        return $this->display();
    }

}
