<?php
namespace wocenter\libs;

use wocenter\backend\modules\passport\models\PassportForm;
use wocenter\backend\modules\passport\models\SecurityForm;
use wocenter\Wc;
use Yii;
use yii\data\Pagination;
use yii\db\Query;

/**
 * 常用函数类库
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Utils
{

    /**
     * 是否超级管理员
     *
     * @param integer $uid 用户ID
     *
     * @return boolean
     */
    public static function isAdministrator($uid = 0)
    {
        if (empty($uid)) {
            $uid = Yii::$app->getUser()->getIdentity()->getId();
        }

        return in_array($uid, (array)Yii::$app->params['superAdmin']);
    }

    /**
     * 获取客户端IP地址
     *
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     *
     * @return mixed
     */
    public static function getClientIp($type = 0, $adv = true)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null)
            return $ip[$type];

        if ($adv) {
            if (isset($_SERVER['HTTP_X_REAL_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_REAL_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos)
                    unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$type];
    }

    /**
     * 获取客户端IP地址信息
     *
     * @return mixed
     */
    public static function getIpLocation()
    {
        $last_ip = static::getClientIp();
        $last_location = '';
        if ($last_ip) {    //如果获取到客户端IP，则获取其物理位置
            $Ip = new IpLocation(); // 实例化类
            $location = $Ip->getlocation($last_ip); // 获取某个IP地址所在的位置

            if ($location['country'] && $location['country'] != 'CZ88.NET')
                $last_location .= $location['country'];

            if ($location['area'] && $location['area'] != 'CZ88.NET')
                $last_location .= ' ' . $location['area'];
        }

        return $last_location;
    }

    /**
     * 格式化字节大小
     *
     * @param  number $size 字节数
     * @param  string $delimiter 数字和单位分隔符
     *
     * @return string 格式化后的带单位的大小
     */
    public static function formatBytes($size, $delimiter = '')
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $size >= 1024 && $i < 5; $i++)
            $size /= 1024;

        return round($size, 2) . $delimiter . $units[$i];
    }

    /**
     * Example:
     *
     * ```php
     * $query = new \yii\db\Query;
     * $query->select('*')
     *   ->from('{{%post}}')
     *   ->where('user_id=:user_id', [':user_id' => $user->id]);
     * $pages = Utils::Pagination($query);
     * $posts = $pages['result'];
     * foreach($posts as $post) {
     *     echo $post['content'];
     * }
     * echo \yii\widgets\LinkPager::widget([
     *   'pagination' => $pages['pages'],
     * ]);
     * ```
     *
     * @param  Query $query SELECT SQL statement
     * @param $defaultPageSize
     *
     * @return array ['result', 'pages']
     */
    public static function pagination($query, $defaultPageSize = 20)
    {
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => $defaultPageSize,
        ]);

        $result = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return ['pages' => $pagination, 'result' => $result];
    }

    /**
     * 根据场景动态显示验证码
     *
     * @param string $scenario 场景 ['signup', 'login', 'find-password', 'reset-password', 'resend-active-email']
     *
     * @return boolean
     */
    public static function showVerify($scenario = '')
    {
        $openVerifyType = Wc::$service->getSystem()->getConfig()->get('VERIFY_OPEN');
        switch ($scenario) {
            case PassportForm::SCENARIO_SIGNUP:
                return in_array($openVerifyType, [1, 2]);
            case PassportForm::SCENARIO_LOGIN:
                return in_array($openVerifyType, [1, 3]);
            case PassportForm::SCENARIO_SIGNUP_BY_INVITE:
            case SecurityForm::SCENARIO_FIND_PASSWORD:
            case SecurityForm::SCENARIO_RESET_PASSWORD:
            case SecurityForm::SCENARIO_ACTIVE_ACCOUNT:
            case SecurityForm::SCENARIO_CHANGE_PASSWORD:
                return $openVerifyType == 1;
            default:
                return $openVerifyType == 1;
        }
    }

}
