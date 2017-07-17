<?php
namespace wocenter\traits;

use wocenter\Wc;
use wocenter\libs\Utils;
use Yii;

/**
 * Class CheckRuleTrait
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait CheckRuleTrait
{

    /**
     * 权限检测
     *
     * @param string $rule 检测的规则
     * @param string $mode check模式
     *
     * @return boolean
     */
    final protected function checkRule($rule, $mode = 'url')
    {
        // 管理员允许访问任何页面
        if (Utils::isAdministrator()) {
            return true;
        }
        static $Auth = null;
        if (!$Auth) {
            $Auth = Wc::$service->getRbac();
        }
        if (!$Auth->check($rule, Yii::$app->getUser()->id, $mode)) {
            return false;
        }

        return true;
    }

}
