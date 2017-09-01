<?php
namespace wocenter\traits;

use wocenter\Wc;
use Yii;

/**
 * Class ApplicationTrait
 * 为Application增加自动加载模块功能
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ApplicationTrait
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        // 创建Wc实例
        Yii::$container->get('Wc');

        $this->loadEnableModules();
        $this->loadUrlRule();
        $this->loadBootstrap();

        parent::init();
    }

    /**
     * 加载系统模块
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function loadEnableModules()
    {
        foreach (Wc::$service->getModularity()->getInstalledModuleConfigs() as $name => $config) {
            if (!$this->hasModule($name)) {
                $this->setModule($name, $config);
            }
        }
    }

    /**
     * 加载系统模块路由规则
     */
    protected function loadUrlRule()
    {
        $this->getUrlManager()->addRules(Wc::$service->getModularity()->getLoad()->getUrlRules());
    }

    /**
     * 加载需要启用bootstrap的模块
     */
    protected function loadBootstrap()
    {
        $this->bootstrap = array_merge($this->bootstrap, Wc::$service->getModularity()->getLoad()->getBootstraps());
    }

}
