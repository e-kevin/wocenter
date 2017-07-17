<?php
namespace wocenter\backend\core;

use wocenter\core\Controller as baseController;
use wocenter\Wc;
use Yii;

/**
 * 后台基础Controller类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Controller extends baseController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app->getUser()->getIsGuest()) {
            if (Wc::$service->getSystem()->getConfig()->get('LOGIN_REMIND')) {
                return $this->error('身份验证信息已过期，正在跳转至登录页面~', $this->getLoginUrl(), 1);
            } else {
                $this->redirect($this->getLoginUrl());
                Yii::$app->end();
            }
        }
    }

    /**
     * 获取登陆地址
     *
     * todo 自定义是否开启url编码操作
     *
     * @return array
     */
    protected function getLoginUrl()
    {
        $request = Yii::$app->getRequest();
        /**
         * 当Yii::$app->getUser()->loginUrl为字符串且为跨模块地址时（如：/passport/common/login），
         * yii\filters\AccessControl无法正确跳转。这种情况需要设置该地址为数组格式，['/passport/common/login']
         */
        $loginUrl = (array)Yii::$app->getUser()->loginUrl;
        $loginUrl[Yii::$app->params['redirect']] = base64_encode($this->isFullPageLoad($request) ?
            $request->getUrl() :
            $request->getReferrer() // 不跳转至post操作的地址
        );

        return $loginUrl;
    }

}