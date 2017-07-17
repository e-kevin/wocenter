<?php
namespace wocenter\services;

use wocenter\core\Service;
use Yii;

/**
 * 系统服务类
 *
 * @property \wocenter\services\system\ConfigService $config
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class SystemService extends Service
{

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'system';
    }

    /**
     * 系统配置服务类
     *
     * @return \wocenter\services\system\ConfigService
     */
    public function getConfig()
    {
        return $this->getSubService('config');
    }

}
