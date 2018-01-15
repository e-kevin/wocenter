<?php

namespace wocenter\traits;

use wocenter\Wc;
use Yii;
use yii\{
    base\Exception, base\InvalidConfigException, helpers\ArrayHelper, web\Application
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
     * @var array WoCenter运行环境需要使用的组件
     */
    public $mustBeSetComponents = ['db', 'extensionService', 'commonCache'];
    
    /**
     * @inheritdoc
     */
    public function preInit(&$config)
    {
        if ($this instanceof Application) {
            try {
                parent::preInit($config);
                // 检查组件是否满足
                foreach ($this->mustBeSetComponents as $component) {
                    if (!isset($config['components'][$component])) {
                        throw new InvalidConfigException("The '{$component}' component for the Application is required.");
                    }
                    $this->set($component, ArrayHelper::remove($config['components'], $component));
                }
                $this->initEnvironment($config);
            } catch (Exception $e) {
                echo nl2br($e->getMessage());
                exit(1);
            }
        } else {
            parent::preInit($config);
            // 检查组件是否满足
            foreach ($this->mustBeSetComponents as $component) {
                if (!isset($config['components'][$component])) {
                    throw new InvalidConfigException("The '{$component}' component for the Application is required.");
                }
                $this->set($component, ArrayHelper::remove($config['components'], $component));
            }
            $this->initEnvironment($config);
        }
    }
    
    /**
     * 初始化WoCenter运行环境
     *
     * @param array $config 应用配置数组
     */
    protected function initEnvironment(&$config)
    {
        $this->id = ArrayHelper::remove($config, 'id');
        // 创建Wc实例
        Yii::$container->get('Wc');
        // 加载扩展配置信息
        $this->loadExtensionConfig($config);
    }
    
    /**
     * 加载扩展配置信息
     *
     * @param array $config
     */
    protected function loadExtensionConfig(array &$config)
    {
        $config = ArrayHelper::merge(Wc::$service->getExtension()->getLoad()->generateConfig(), $config);
        foreach ($config['components'] as $name => $row) {
            // 剔除已经实例化的组件
            if ($this->has($name)) {
                unset($config['components'][$name]);
            }
        }
    }
    
}
