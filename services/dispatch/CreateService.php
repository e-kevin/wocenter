<?php
namespace wocenter\services\dispatch;

use wocenter\core\Controller;
use wocenter\core\Dispatch;
use wocenter\core\Service;
use wocenter\helpers\ArrayHelper;
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
     * 例如：ConfigManager控制器，路由地址为'config-manager'，调度器在处理路由地址时，因命名空间不支持带'-'的命名方式，
     * 因此需要处理该字符窜，操作将返回如`configManager`这样格式的字符窜
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
            if (isset($dispatchMap[$id])) {
                $config = $dispatchMap[$id];
                /**
                 * 调度配置为数组，支持以下键名配置
                 *  - `class`: 使用该类创建所需调度器。用法和[[actions()]]方法相同，唯一区别是该类必须继承`wocenter\core\Dispatch`。
                 * 当该键名被指定，则以下键名配置将不生效
                 *  - `dispatchOptions`: 调度器配置，可以使用的配置键如下：
                 *   - `theme`: 是否调用指定主题的调度器。可能的值如下：
                 *    - false: 禁用控制器[Controller::$dispatchTheme]]配置
                 *    - string: 用户自定义的主题
                 *   - `path`: 是否设置自定义调度器基础路径，系统会调用该路径下指定路由的调度器，使用别名路径。可能的值如下：
                 *    - false: 禁用控制器[Controller::$dispatchBasePath]]配置
                 *    - string: 用户自定义的基础路径
                 *   - `map`: 使用其他调度器映射。如：
                 *   ```php
                 *      'update' => [
                 *          'dispatchOptions' => [
                 *              'map' => 'edit', // 将调用'Edit'调度器替代原来的'Update'调度器
                 *           ]
                 *      ]
                 *   ```
                 * 或者：
                 *   ```php
                 *      'update' => 'edit', // 直接使用调度器映射
                 *   ```
                 */
                if (is_array($config)) {
                    $dispatchOptions = ArrayHelper::remove($config, 'dispatchOptions', []);
                    // 使用该类相关配置创建所需调度器
                    if (isset($config['class'])) {
                        $classConfig = $config;
                    } // 默认使用系统核心调度器类
                    else {
                        $this->_setDispatchOptions([
                            'theme' => isset($dispatchOptions['theme'])
                                ? ($dispatchOptions['theme'] === false) ? null : $dispatchOptions['theme']
                                : $controller->dispatchTheme,
                            'path' => isset($dispatchOptions['path'])
                                ? ($dispatchOptions['path'] === false) ? null : $dispatchOptions['path']
                                : $controller->dispatchBasePath,
                        ]);
                        // 使用其他调度器映射
                        $route = $this->getUniqueId() . '/' . Inflector::camelize(isset($dispatchOptions['map'])
                                ? $dispatchOptions['map']
                                : $id
                            );
                        $classConfig = array_merge(['class' => $this->service->getNamespace($route)], $config);
                    }
                }  // 调度配置为类名
                elseif (class_exists($config)) {
                    $classConfig = $config;
                } // 调度器映射
                else {
                    $this->_setDispatchOptions([
                        'theme' => $controller->dispatchTheme,
                        'path' => $controller->dispatchBasePath,
                    ]);
                    $route = $this->getUniqueId() . '/' . Inflector::camelize($config);
                    $classConfig = $this->service->getNamespace($route);
                }
            } // 调用系统默认调度器
            else {
                $this->_setDispatchOptions([
                    'theme' => $controller->dispatchTheme,
                    'path' => $controller->dispatchBasePath,
                ]);
                $route = $this->getUniqueId() . '/' . Inflector::camelize($id);
                $classConfig = $this->service->getNamespace($route);
            }

            if (($dispatch = $this->create($id, $classConfig, $controller)) === null) {
                $this->generateDispatchFile(is_array($classConfig) ? $classConfig['class'] : $classConfig);
            }

            return $dispatch;
        }

        return null;
    }

    /**
     * 调度器不存在则抛出友好提示信息
     *
     * @param string $className 调度器类名
     *
     * @throws Exception
     */
    public function generateDispatchFile($className)
    {
        $file = '@' . str_replace('\\', '/', $className) . '.php';
        $file = str_replace('\\', DIRECTORY_SEPARATOR, Yii::getAlias($file));
        throw new Exception("请在该路径下创建调度器文件:\r\n{$file}");
    }

    /**
     * 设置调度器配置参数
     *
     * @param array $dispatchOptions
     */
    protected function _setDispatchOptions($dispatchOptions = [])
    {
        // 是否调用指定主题的调度器
        if (isset($dispatchOptions['theme']) && $dispatchOptions['theme'] !== null) {
            $this->service->getView()->themeName = $dispatchOptions['theme'];
        }
        // 是否设置自定义调度器基础路径，系统会调用该路径下指定路由的调度器
        if (isset($dispatchOptions['path']) && $dispatchOptions['path'] !== null) {
            $this->service->getView()->basePath = $dispatchOptions['path'];
        }
    }

    /**
     * 创建调度器
     *
     * @param string $id 调度器ID，一般为调度器类名
     * @param string|array $classConfig 调度器类名或调度器配置信息
     * @param Controller $controller 调用调度器的控制器
     *
     * @return null|Dispatch
     * @throws InvalidConfigException
     */
    public function create($id, $classConfig, $controller)
    {
        if (!is_array($classConfig)) {
            $classConfig = ['class' => $classConfig];
        }

        $className = $classConfig['class'];

        if (!class_exists($className)) {
            return null;
        } elseif (is_subclass_of($className, 'wocenter\core\Dispatch')) {
            // 转换调度器ID为调度器所属视图文件ID
            $id = Inflector::camel2id($id);
            $dispatch = Yii::createObject($classConfig, [$id, $controller]);

            return get_class($dispatch) === $className ? $dispatch : null;
        } elseif (YII_DEBUG) {
            throw new InvalidConfigException("Dispatch class must extend from \\wocenter\\core\\Dispatch.");
        }

        return null;
    }

}
