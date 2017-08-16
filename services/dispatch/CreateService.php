<?php
namespace wocenter\services\dispatch;

use wocenter\core\Controller;
use wocenter\core\Dispatch;
use wocenter\core\Service;
use wocenter\services\DispatchService;
use Yii;
use yii\base\Application;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

/**
 * 创建调度器服务类
 *
 * @property string $uniqueId 调度器唯一标识
 * @author E-Kevin <e-kevin@qq.com>
 */
class CreateService extends Service
{

    /**
     * @var DispatchService 父级服务类
     */
    public $service;

    /**
     * @var Controller 当前激活的控制器
     */
    protected $controller;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'create';
    }

    /**
     * 调度器唯一标识，补充对系统应用模块的支持
     *
     * @return string
     */
    public function getUniqueId()
    {
        $controller = Yii::$app->controller;
        $moduleId = $controller->module instanceof Application ? $controller->module->id : $controller->module->getUniqueId();
        $uniqueId = explode('/', $moduleId . '/' . $controller->id);
        foreach ($uniqueId as &$part) {
            $part = $this->normalizeName($part);
        }

        return implode('/', $uniqueId);
    }

    /**
     * 格式化带'-'的字符窜
     * 例如：ConfigManager控制器，路由地址为'config-manager'，调度器在处理路由地址时，因命名空间不支持带'-'的命名方式，因此需要
     * 处理该字符窜，操作将返回如`configManager`这样格式的字符窜
     *
     * @param string $string
     *
     * @return string
     */
    public function normalizeName($string)
    {
        if (($pos = strpos($string, '-')) !== false) {
            $string = Inflector::variablize($string);
        }

        return $string;
    }

    /**
     * 根据调度配置创建调度器
     *
     * @param string $id 调度器ID
     *
     * @return null|Dispatch
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function createByConfig($id)
    {
        /** @var Controller $controller */
        $controller = Yii::$app->controller;
        if ($id === '') {
            $id = $controller->defaultAction;
        }
        // 系统常规创建动作失败后则调用系统调度器继续执行路由请求，如果存在调度配置信息则执行自定义调度，否则终止调度行为
        $dispatchMap = $controller->dispatches();
        // 存在自定义调度配置
        if (in_array($id, $dispatchMap) || isset($dispatchMap[$id])) {
            /**
             * 自定义个别调度器，支持以下键名配置
             *  - `class`: 直接使用该类创建所需调度器
             *  - `theme`: 是否调用指定主题的调度器
             *  - `path`: 是否指定自定义调度器基础路径，系统会调用该路径下指定路由的调度器
             *  - `map`: 使用其他调度器映射。如：
             *  ```php
             *      'update' => ['map'=>'edit'], // 将调用'Edit'调度器替代原本的'Update'调度器
             *  ```
             */
            if (isset($dispatchMap[$id])) {
                $config = $dispatchMap[$id];
                // 直接使用该类创建所需调度器
                if (isset($config['class'])) {
                    return $this->create($id, $config['class'], $controller);
                } elseif (is_array($config)) {
                    // 是否调用指定主题的调度器
                    if (isset($config['theme'])) {
                        $this->service->theme = $config['theme'];
                    }
                    // 是否指定自定义调度器基础路径，系统会调用该路径下指定路由的调度器
                    if (isset($config['path'])) {
                        $this->service->getView()->basePath = $config['path'];
                    }
                    // 使用其他调度器映射
                    $route = $this->getUniqueId() . '/' . Inflector::camelize(isset($config['map'])
                            ? $config['map']
                            : $id
                        );
                } else {
                    $route = $this->getUniqueId() . '/' . Inflector::camelize($dispatchMap[$id]);
                }
                $className = $this->service->getNamespace($route);
            } else {
                // 是否调用指定主题的调度器
                if ($controller->dispatchTheme !== null) {
                    $this->service->theme = $controller->dispatchTheme;
                }
                // 是否指定自定义调度器基础路径，系统会调用该路径下指定路由的调度器
                if ($controller->dispatchBasePath !== null) {
                    $this->service->getView()->basePath = $controller->dispatchBasePath;
                }
                $route = $this->getUniqueId() . '/' . Inflector::camelize($id);
                $className = $this->service->getNamespace($route);
            }

            if (($dispatch = $this->create($id, $className, $controller)) === null) {
                $file = '@' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
                throw new Exception("请在该路径下创建调度文件:\r\n{$file}");
            }

            return $dispatch;
        }

        return null;
    }

    /**
     * 创建调度器
     *
     * @param string $id 调度器ID，一般为调度器类名
     * @param string $className 调度器类名
     * @param null|Controller $controller 调用调度器的控制器
     *
     * @return null|Dispatch
     * @throws InvalidConfigException
     */
    public function create($id, $className, $controller = null)
    {
        if (!class_exists($className)) {
            return null;
        }
        if (is_subclass_of($className, 'wocenter\core\Dispatch')) {
            if ($controller === null) {
                $controller = Yii::$app->controller;
                if ($controller === null) {
                    $controller = Yii::createObject('yii\web\Controller', ['common', null]);
                }
            }

            // 转换调度器ID为调度器所属视图文件ID
            $id = Inflector::camel2id($id);
            $dispatch = Yii::createObject($className, [$id, $controller]);

            return get_class($dispatch) === $className ? $dispatch : null;
        } elseif (YII_DEBUG) {
            throw new InvalidConfigException("Dispatch class must extend from \\wocenter\\core\\Dispatch.");
        }

        return null;
    }

}
