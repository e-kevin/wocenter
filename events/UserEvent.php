<?php

namespace wocenter\events;

use wocenter\{
    core\ActiveRecord, core\Model, interfaces\IdentityInterface, backend\modules\account\models\User as BackendUser
};

/**
 * Class UserEvent
 * 用户事件类，主要是用于更正IDE支持
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
