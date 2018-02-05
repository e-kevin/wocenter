<?php

namespace wocenter\core;

use wocenter\Wc;
use Yii;
use yii\{
    base\Theme, web\View as baseView
};

/**
 * 基础View类
 *
 * @property string $themeName
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class View extends baseView
{
    
    /**
     * @var string 主题名称
     */
    private $_themeName;
    
    /**
     * @inheritdoc
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        if ($this->theme == null) {
            $this->setPathMap();
        }
        
        return parent::renderFile($viewFile, $params, $context);
    }
    
    /**
     * 设置视图路径映射
     */
    protected function setPathMap()
    {
        // 路径映射从上到下依次代表优先级由高到低，只要获取到有效映射则返回结果，否则继续往下获取
        $config['pathMap'] = [
            '@app/views' => [
                '@app/views',
                Wc::$service->getExtension()->getTheme()->getCurrentTheme()->getViewPath(), // 添加对主题扩展的支持
            ],
            // 添加对开发者扩展的支持
            '@developer' => [
                '@developer',
                '@extensions',
            ],
        ];
        
        $this->theme = new Theme($config);
    }
    
    /**
     * 获取主题名称
     *
     * @return string 如：adminlte、basic等
     */
    public function getThemeName()
    {
        if ($this->_themeName == null) {
            $this->_themeName = Wc::$service->getExtension()->getTheme()->getCurrentTheme()->id;
        }
        
        return $this->_themeName;
    }
    
}
