<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\core\Controller;
use wocenter\models\User;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'init-password' => ['post'],
                    'generate' => ['post'],
                    'delete' => ['post'],
                    'active' => ['post'],
                    'forbidden' => ['post'],
                    'unlock' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            'view',
            'update',
            'search',
            'generate',
            'init-password',
            'profile',
        ];
    }

    /**
     * 用户列表
     */
    public function actionIndex()
    {
        return $this->_getUserList(User::STATUS_ACTIVE);
    }

    /**
     * 用户禁用列表
     */
    public function actionForbiddenList()
    {
        return $this->_getUserList(User::STATUS_FORBIDDEN);
    }

    /**
     * 用户锁定列表
     */
    public function actionLockedList()
    {
        return $this->_getUserList(User::STATUS_LOCKED);
    }

    /**
     * 获取用户列表
     *
     * @param integer $status
     *
     * @return mixed
     */
    private function _getUserList($status)
    {
        return $this->getDispatch('index')->run($status);
    }

    /**
     * 删除用户
     *
     * @return mixed
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionDelete()
    {
        return $this->changeStatus('delete');
    }

    /**
     * 禁闭用户
     *
     * @return mixed
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionForbidden()
    {
        return $this->changeStatus('forbidden');
    }

    /**
     * 激活用户
     *
     * @return mixed
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionActive()
    {
        return $this->changeStatus('active');
    }

    /**
     * 解锁用户
     *
     * @return mixed
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionUnlock()
    {
        return $this->changeStatus('unlock');
    }

    /**
     * 更改用户状态
     *
     * @param string $method
     *
     * @return mixed
     * @author E-Kevin <e-kevin@qq.com>
     */
    protected function changeStatus($method)
    {
        return $this->getDispatch('change-status')->run($method);
    }

}
