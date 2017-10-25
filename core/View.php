<?php

namespace wocenter\core;

use wocenter\Wc;
use yii\base\Theme;
use yii\web\View as baseView;

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
     * @var boolean 视图映射是否严谨，默认为`false`，即宽松模式：只要开发者目录内存在相应视图文件即可被渲染
     * TODO 添加数据库配置
     */
    public $strict = false;
    
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
        // TODO 是否自定义[[Theme()]]类为已经获取到的映射文件做缓存？
        /**
         * 返回值请查看
         * [多模板系统](https://github.com/Wonail/wocenter_doc/blob/master/guide/zh-CN/mutil-theme.md#%E4%BC%98%E5%85%88%E7%BA%A7)
         */
        // 严谨模式
        if ($this->strict) {
        } // 宽松模式
        else {
        }
        $config['pathMap'] = [
            '@app/views' => [
                '@app/views',
                Wc::$service->getExtension()->getTheme()->getCurrentTheme()->getViewPath(),
            ],
            '@extensions' => [
                '@app/extensions',
                '@extensions',
            ],
        ];
        
        $this->theme = new Theme($config);
    }

    /**
     * 获取主题名称
     *
     * @return string
     */
    public function getThemeName()
    {
        if ($this->_themeName == null) {
            if (\Yii::$app->id == 'backend') {
                $this->_themeName = Wc::$service->getSystem()->getConfig()->get('BACKEND_THEME', 'basic');
            } else {
                $this->_themeName = 'basic';
            }
        }

        return $this->_themeName;
    }

    /**
     * 设置主题名称
     *
     * @param string $themeName 主题名称
     */
    public function setThemeName($themeName)
    {
        $this->_themeName = $themeName;
    }
    
}
