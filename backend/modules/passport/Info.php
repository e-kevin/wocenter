<?php
namespace wocenter\backend\modules\passport;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    public function init()
    {
        parent::init();
        
        $this->id = 'passport';
        $this->name = '通行证管理';
        $this->version = '1.0';
        $this->developer = 'WoCenter';
        $this->email = 'e-kevin@qq.com';
        $this->description = '提供登录、注册、密码找回、验证码等与账户安全相关的服务';
        $this->isSystem = true;
    }

    /**
     * 模块菜单信息

     * @return array
     */
    public function getMenus()
    {
    }
}
