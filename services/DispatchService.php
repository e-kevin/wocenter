<?php
namespace wocenter\services;

use wocenter\core\Service;
use wocenter\core\View;
use Yii;
use yii\base\InvalidConfigException;

/**
 * 调度服务类
 *
 * @property View $view Dispatch需要使用的view组件，默认使用Yii::$app->getView()组件
 * @property string $basePath 调度器基础路径，只读属性
 * @property string $commonNamespace 获取主题公共调度器，默认调度器为主题目录下的components文件夹的Dispatch文件，只读属性
 *
 * @property \wocenter\services\dispatch\CreateService $create 创建调度器服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DispatchService extends Service
{

    /**
     * @var string 调用指定主题的调度器，默认为Yii::$app->getView()组件设置的[[$themeName]]主题
     * @see wocenter\core\View::$themeName
     */
    public $theme;
    
    /**
     * @var string 调度器基础路径
     */
    protected $_basePath;

    /**
     * @var View Dispatch需要使用的view组件，默认使用Yii::$app->getView()组件
     * @see wocenter\core\View
     */
    protected $_view;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'dispatch';
    }
    
    /**
     * 获取Dispatch需要使用的view组件
     *
     * @return View
     */
    public function getView()
    {
        if ($this->_view == null) {
            $this->setView(Yii::$app->getView());
        }

        return $this->_view;
    }

    /**
     * 设置Dispatch需要使用的view组件
     *
     * @param View $view
     *
     * @throws InvalidConfigException
     */
    public function setView($view)
    {
        if (!$view instanceof View) {
            throw new InvalidConfigException('The Dispatch Service needs to be used by the view component to inherit `\wocenter\core\View`');
        }
        $this->_view = $view;
    }

    /**
     * 获取调度目录基础路径
     * 系统调度器默认存放在主题目录内，故调用[[View]]组件设置的主题路径
     *
     * @param string $path 主题目录下的路径，默认为主题下的调度器目录
     *
     * @return string
     */
    public function getBasePath($path = 'dispatch')
    {
        // 是否调用指定主题的调度器
        if ($this->theme !== null) {
            $this->getView()->themeName = $this->theme;
        }

        return $this->getView()->getThemePath($path);
    }

    /**
     * 获取指定路由地址的调度器命名空间，默认获取wocenter调度器目录的命名空间
     *
     * @param string $route 路由地址
     * @param string $path 主题目录下的路径，默认为主题下的调度器目录
     *
     * @return string 调度器命名空间
     */
    public function getNamespace($route, $path = 'dispatch')
    {
        return str_replace(DIRECTORY_SEPARATOR, '\\', substr($this->getBasePath($path) . '/' . $route, 1));
    }

    /**
     * 获取主题公共调度器，默认调度器为主题目录下的components文件夹的Dispatch文件
     *
     * @return string 调度器命名空间
     */
    public function getCommonNamespace()
    {
        return $this->getNamespace('Dispatch', 'components');
    }

    /**
     * 创建调度器服务类
     *
     * @return \wocenter\services\dispatch\CreateService
     */
    public function getCreate()
    {
        return $this->getSubService('create');
    }

}
