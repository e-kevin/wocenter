<?php
namespace wocenter\events;

use wocenter\core\ActiveRecord;
use wocenter\core\Model;
use wocenter\interfaces\IdentityInterface;
use wocenter\models\User;

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
