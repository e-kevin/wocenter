<?php
namespace wocenter\events;

use wocenter\core\ActiveRecord;
use wocenter\core\Model;
use wocenter\interfaces\IdentityInterface;
use wocenter\models\User;

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
     * @var IdentityInterface|User 用户对象类
     */
    public $identity;
}
