<?php
namespace wocenter\backend\modules\system\controllers;

use wocenter\backend\core\Controller;

class SettingController extends Controller
{

    /**
     * 基本配置
     *
     * @return mixed
     */
    public function actionBasic()
    {
        return $this->_update(1);
    }

    /**
     * 内容配置
     *
     * @return mixed
     */
    public function actionContent()
    {
        return $this->_update(2);
    }

    /**
     * 注册配置
     *
     * @return mixed
     */
    public function actionRegister()
    {
        return $this->_update(3);
    }

    /**
     * 系统配置
     *
     * @return mixed
     */
    public function actionConfig()
    {
        return $this->_update(4);
    }

    /**
     * 安全配置
     *
     * @return mixed
     */
    public function actionSecurity()
    {
        return $this->_update(5);
    }

    protected function _update($id)
    {
        return $this->getDispatch('update')->run($id);
    }

}
