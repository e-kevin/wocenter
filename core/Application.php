<?php
namespace wocenter\core;

use wocenter\Wc;
use Yii;
use yii\web\Application as baseApplication;

/**
 * 基础Application类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Application extends baseApplication
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        // 创建Wc实例
        Yii::$container->get('Wc');

        $this->loadEnableModules();

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

}
