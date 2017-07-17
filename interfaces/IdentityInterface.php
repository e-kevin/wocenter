<?php
namespace wocenter\interfaces;

/**
 * 用户认证接口类
 *
 * @package wocenter\interfaces
 * @author E-Kevin <e-kevin@qq.com>
 */
interface IdentityInterface extends \yii\web\IdentityInterface
{

    /**
     * 激活状态
     */
    const STATUS_ACTIVE = 1;
    /**
     * 禁用状态
     */
    const STATUS_FORBIDDEN = 0;
    /**
     * 删除状态
     */
    const STATUS_DELETED = -1;
    /**
     * 锁定状态
     */
    const STATUS_LOCKED = -3;

    /**
     * 根据用户标识查询用户信息
     *
     * @param string|integer $identity 用户标识 [手机，邮箱，用户名]
     * @param array $conditions 查询条件
     *
     * @return null|static
     */
    public static function findByIdentity($identity, $conditions = []);
}
