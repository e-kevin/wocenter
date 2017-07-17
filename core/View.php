<?php
namespace wocenter\core;

use Yii;
use yii\base\Theme;
use yii\web\View as baseView;

/**
 * 基础View类
 *
 * @property string $basePath 主题路径，使用别名，默认为系统主题路径
 * @author E-Kevin <e-kevin@qq.com>
 */
class View extends baseView
{

    /**
     * @var string 主题名称，默认为`basic`
     */
    public $themeName = 'basic';

    /**
     * @var string 主题路径，使用别名
     */
    private $_basePath;

    public function renderFile($viewFile, $params = [], $context = null)
    {
        if ($this->theme == null) {
            $this->setTheme();
        }

        return parent::renderFile($viewFile, $params, $context);
    }

    /**
     * 设置主题，模块视图路径映射
     */
    protected function setTheme()
    {
        $app = Yii::$app->id;
        $config['pathMap'] = [
            '@app/views' => [
                $this->getThemePath('views'),
            ],
            "@wocenter/{$app}/modules" => [
                $this->getThemePath('modules'),
            ],
        ];

        $this->theme = new Theme($config);
    }

    /**
     * 获取主题目录路径
     *
     * @return string
     */
    public function getBasePath()
    {
        if ($this->_basePath == null) {
            $this->_basePath = '@wocenter' . DIRECTORY_SEPARATOR . Yii::$app->id . DIRECTORY_SEPARATOR . 'themes';
        }

        return $this->_basePath;
    }

    /**
     * 设置主题目录路径
     *
     * @param string $path 主题路径，支持别名
     */
    public function setBasePath($path)
    {
        $this->_basePath = $path;
    }

    /**
     * 获取指定主题目录内相关路径
     *
     * @param null $path 主题内路径
     *
     * @return string
     */
    public function getThemePath($path = null)
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . $this->themeName . DIRECTORY_SEPARATOR . ($path ?: '');
    }

}
