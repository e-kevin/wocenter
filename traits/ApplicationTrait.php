<?php

namespace wocenter\traits;

use wocenter\Wc;
use Yii;
use yii\base\Module;
use yii\web\UrlManager;

/**
 * Class ApplicationTrait
 * 扩展Application，为WoCenter构建运行所需条件
 *
 * @method UrlManager getUrlManager()
 * @method boolean hasModule($id)
 * @method void setModule($id, $module)
 * @method Module|null getModule($id, $load = true)
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

        $this->loadExtensionAliases();
        $this->loadEnableModules();
        $this->loadControllers();
        $this->loadUrlRule();
        $this->loadBootstrap();
        
        parent::init();
    }
    
    /**
     * 加载扩展别名
     */
    protected function loadExtensionAliases()
    {
        Wc::$service->getExtension()->getLoad()->loadAliases();
    }
    
    /**
     * 加载系统模块
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function loadEnableModules()
    {
        foreach (Wc::$service->getExtension()->getModularity()->getInstalledConfig() as $moduleId => $config) {
            if (!$this->hasModule($moduleId)) {
                $this->setModule($moduleId, $config);
            }
        }
    }
    
    /**
     * 加载应用控制器扩展
     */
    protected function loadControllers()
    {
        foreach (Wc::$service->getExtension()->getController()->getAppInstalledConfig() as $controllerId => $config) {
            if (!isset($this->controllerMap[$controllerId])) {
                $this->controllerMap[$controllerId] = $config;
            }
        }
    }
    
    /**
     * 加载系统模块路由规则
     */
    protected function loadUrlRule()
    {
        $this->getUrlManager()->addRules(Wc::$service->getExtension()->getModularity()->getUrlRules());
    }
    
    /**
     * 加载需要启用bootstrap的模块
     */
    protected function loadBootstrap()
    {
        $this->bootstrap = array_merge($this->bootstrap, Wc::$service->getExtension()->getModularity()->getBootstraps());
    }
    
}
