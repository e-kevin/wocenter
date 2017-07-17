<?php
namespace wocenter\backend\modules\account\controllers;

use wocenter\models\User;
use wocenter\backend\core\Controller;
use yii\filters\VerbFilter;

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
     * 用户列表
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->_getUserList(['status' => User::STATUS_ACTIVE]);
    }

    /**
     * 用户禁用列表
     *
     * @return string|\yii\web\Response
     */
    public function actionForbiddenList()
    {
        return $this->_getUserList(['status' => User::STATUS_FORBIDDEN]);
    }

    /**
     * 用户锁定列表
     *
     * @return string|\yii\web\Response
     */
    public function actionLockedList()
    {
        return $this->_getUserList(['status' => User::STATUS_LOCKED]);
    }

    /**
     * 获取用户列表
     *
     * @param $params
     *
     * @return string|\yii\web\Response
     */
    private function _getUserList($params)
    {
        return $this->getDispatch('index')->setParams($params)->run();
    }

    /**
     * 搜索操作
     *
     * @return string|\yii\web\Response
     */
    public function actionSearch()
    {
        return $this->runDispatch();
    }

    /**
     * Displays a single User model.
     *
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->setParams('id', $id)->run();
    }

    /**
     * Updates an existing User model.
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        return $this->setParams('id', $id)->run();
    }

    /**
     * 删除用户
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionDelete()
    {
        return $this->changeStatus('delete');
    }

    /**
     * 禁闭用户
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionForbidden()
    {
        return $this->changeStatus('forbidden');
    }

    /**
     * 激活用户
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionActive()
    {
        return $this->changeStatus('active');
    }

    /**
     * 解锁用户
     *
     * @author E-Kevin <e-kevin@qq.com>
     * @return mixed
     */
    public function actionUnlock()
    {
        return $this->changeStatus('unlock');
    }

    /**
     * 更改用户状态
     *
     * @author E-Kevin <e-kevin@qq.com>
     *
     * @param $method string
     *
     * @return mixed
     */
    protected function changeStatus($method)
    {
        return $this->getDispatch('change-status')->setParams('method', $method)->run();
    }

    /**
     * 生成随机用户
     *
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionGenerate()
    {
        return $this->runDispatch();
    }

    /**
     * 重置用户密码
     *
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionInitPassword()
    {
        return $this->runDispatch();
    }

    /**
     * 用户个人资料
     *
     * @author E-Kevin <e-kevin@qq.com>
     */
    public function actionProfile()
    {
        return $this->runDispatch();
    }

}
