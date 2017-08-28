<?php
namespace wocenter\backend\modules\passport;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->name = '通行证管理';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '提供登录、注册、密码找回、验证码等与账户安全相关的服务';
        $this->isSystem = true;
    }

    /**
     * @inheritdoc
     */
    public function getUrlRules()
    {
        return [
            'login' => "{$this->getId()}/common/login",
            'logout' => "{$this->getId()}/common/logout",
            'logout-on-step' => "{$this->getId()}/common/logout-on-step",
            'signup' => "{$this->getId()}/common/signup",
            'step' => "{$this->getId()}/common/step",
            'invite-signup' => "{$this->getId()}/common/invite-signup",
            'find-password' => "{$this->getId()}/security/find-password",
            'find-password-successful' => "{$this->getId()}/security/find-password-successful",
            'reset-password' => "{$this->getId()}/security/reset-password",
            'activate-account' => "{$this->getId()}/security/activate-account",
            'activate-user' => "{$this->getId()}/security/activate-user",
            'change-password' => "{$this->getId()}/security/change-password",
        ];
    }

}
