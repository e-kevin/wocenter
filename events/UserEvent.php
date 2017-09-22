<?php
namespace wocenter\events;

use wocenter\core\ActiveRecord;
use wocenter\core\Model;
use wocenter\interfaces\IdentityInterface;
use wocenter\backend\modules\account\models\User as BackendUser;

/**
 * Class UserEvent
 * 用户事件类，主要是用于更正IDE支持
 *
 * @package wocenter\events
 */
class UserEvent extends \yii\web\UserEvent
{
    /**
     * @var Model|ActiveRecord
     */
    public $sender;

    /**
     * @var IdentityInterface|BackendUser 用户对象类
     */
    public $identity;
}
