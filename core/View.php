<?php
namespace wocenter\core;

use wocenter\Wc;
use Yii;
use yii\base\Theme;
use yii\web\View as baseView;

/**
 * 基础View类
 *
 * @property string $baseThemePath 开发者主题基础路径，使用别名
 * @author E-Kevin <e-kevin@qq.com>
 */
class View extends baseView
{

    /**
     * @var string 主题名称，默认为`basic`
     */
    public $themeName = 'basic';

    /**
     * @var boolean 视图映射是否严谨，默认为`false`，即宽松模式：只要开发者目录内存在相应视图文件即可被渲染
     * TODO 添加数据库配置
     */
    public $strict = false;

    /**
     * @var string 开发者主题基础路径，使用别名
     */
    private $_baseThemePath;

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
        // 开发者模块路径
        $developerModulePath = Wc::$service->getModularity()->getDeveloperModulePath();
        // 系统核心模块路径
        $coreModulePath = Wc::$service->getModularity()->getCoreModulePath();
        // 路径映射从上到下依次代表优先级由高到低，只要获取到有效映射则返回结果，否则继续往下获取
        // TODO 是否则自定义[[Theme()]]类为已经获取到的映射文件做缓存？
        // e.g. app以backend为例
        /**
         * 返回值请查看
         * [多模板系统](https://github.com/Wonail/wocenter_doc/blob/master/guide/zh-CN/mutil-theme.md#%E4%BC%98%E5%85%88%E7%BA%A7)
         */
        $config['pathMap'] = [
            '@app/views' => [
                '@app/views',
                $this->getDeveloperThemePath('views'),
                $this->getCoreThemePath('views'),
            ],
            $this->getDeveloperThemePath('views') => [
                $this->getDeveloperThemePath('views'),
                $this->getCoreThemePath('views'),
            ],
            $developerModulePath => [
                $developerModulePath,
                $this->getDeveloperThemePath('modules'),
                $this->getCoreThemePath('modules'),
            ],
            $coreModulePath => [
                $developerModulePath,
                $this->getDeveloperThemePath('modules'),
                $this->getCoreThemePath('modules'),
            ],
        ];
        // 严谨模式
        if ($this->strict) {
            if (Wc::$service->getDispatch()->getIsRunningCoreModule()) {
                $config['pathMap'][$developerModulePath] = [
                    $this->getCoreThemePath('modules'),
                    $developerModulePath,
                    $this->getDeveloperThemePath('modules'),
                ];
                $config['pathMap'][$coreModulePath] = [
                    $this->getCoreThemePath('modules'),
                ];
            }
        }

        $this->theme = new Theme($config);
    }

    /**
     * 获取开发者主题基础路径，默认为当前应用下的`themes`文件夹，如：`backend/themes`
     *
     * @return string
     */
    public function getBaseThemePath()
    {
        if ($this->_baseThemePath == null) {
            $this->setBaseThemePath('@app/themes');
        }

        return $this->_baseThemePath;
    }

    /**
     * 设置开发者主题基础路径
     *
     * @param string $alias 主题別名路径
     */
    public function setBaseThemePath($alias)
    {
        // 确保以'@'开头
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $this->_baseThemePath = $alias;
    }

    /**
     * 获取开发者主题目录内相对路径
     *
     * @param null $path 主题内相对路径
     *
     * @return string
     */
    public function getDeveloperThemePath($path = null)
    {
        $path = $this->getBaseThemePath() . '/' . $this->relativeThemePath($path);

        return str_replace('app', Yii::$app->id, $path);
    }

    /**
     * 获取系统核心主题目录内相对路径
     *
     * @param null $path 主题内相对路径
     *
     * @return string
     */
    public function getCoreThemePath($path = null)
    {
        return '@wocenter/' . Yii::$app->id . '/themes/' . $this->relativeThemePath($path);
    }

    /**
     * 获取主题目录内相对路径
     *
     * @param null $path 主题内相对路径
     *
     * @return string
     */
    protected function relativeThemePath($path = null)
    {
        return $this->themeName . (ltrim($path, '/') ? '/' . $path : '');
    }

}
