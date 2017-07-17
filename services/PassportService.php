<?php
namespace wocenter\services;

use wocenter\core\Service;
use Yii;

/**
 * 通行证服务类
 *
 * @property \wocenter\services\passport\UcenterService $ucenter
 * @property \wocenter\services\passport\VerifyService $verify
 * @property \wocenter\services\passport\ValidationService $validation
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class PassportService extends Service
{

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'passport';
    }

    /**
     * 获取认证中心子服务类
     *
     * @return \wocenter\services\passport\UcenterService
     */
    public function getUcenter()
    {
        return $this->getSubService('ucenter');
    }

    /**
     * 获取验证中心子服务类
     *
     * @return \wocenter\services\passport\VerifyService
     */
    public function getVerify()
    {
        return $this->getSubService('verify');
    }

    /**
     * 规则验证服务类
     *
     * @return \wocenter\services\passport\ValidationService
     */
    public function getValidation()
    {
        return $this->getSubService('validation');
    }

}
