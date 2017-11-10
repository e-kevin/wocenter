<?php

namespace wocenter\traits;

use Yii;
use yii\{
    base\Exception, base\InvalidConfigException, web\Application
};

/**
 * Class ApplicationTrait
 * 扩展Application，为WoCenter构建运行所需条件
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ApplicationTrait
{
    
    /**
     * @inheritdoc
     */
    public function preInit(&$config)
    {
        if ($this instanceof Application) {
            try {
                parent::preInit($config);
                // 检查是否已经配置扩展服务
                if (!isset($config['components']['extensionService'])) {
                    throw new InvalidConfigException('The "extensionService" component for the Application is required.');
                }
            } catch (Exception $e) {
                echo nl2br($e->getMessage());
                exit(1);
            }
        } else {
            parent::preInit($config);
            // 检查是否已经配置扩展服务
            if (!isset($config['components']['extensionService'])) {
                throw new InvalidConfigException('The "extensionService" component for the Application is required.');
            }
        }
        
        // 创建Wc实例
        Yii::$container->get('Wc');
    }
    
}
