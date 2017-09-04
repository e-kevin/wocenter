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
            $part = $this->service->normalizeControllerName($part);
        }

        return implode('/', $uniqueId);
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
                 *  - `class`: 使用该类创建调度器，该类必须继承`wocenter\core\Dispatch`。注意：当该值被指定，以下配置将不生效
                 *  - `dispatchOptions`: 调度器配置，可以使用的配置键如下：
                 *   - `theme`: 是否调用指定主题的调度器，系统会调用该主题下指定路由的调度器。可能的值如下：
                 *    - false: 禁用控制器[Controller::$dispatchTheme]]配置
                 *    - string: 用户自定义的主题
                 *   - `themePath`: 是否自定义开发者主题基础路径，系统会调用该路径下指定路由的调度器，使用别名路径，
                 *      默认为'@app/themes'，即为当前应用的themes主题目录。
                 *      详情请看：[[\wocenter\core\View\getBaseThemePath()]]。
                 *      可能的值如下：
                 *    - string: 用户自定义的基础路径
                 *   - `map`: 使用其他调度器映射。如：
                 *   ```php
                 *      'update' => [
                 *          'dispatchOptions' => [
                 *              'map' => 'edit', // 使用调度器配置映射，将调用'Edit'调度器替代原来的'Update'调度器
                 *           ]
                 *      ]
                 *   ```
                 *  或者：
                 *   ```php
                 *      'update' => 'edit', // 直接使用调度器映射
                 *   ```
                 *  注意：配置映射后，如果调度器内使用的是[[display()]]方法进行页面渲染而没有指定方法内的`$view`参数，则该方法将
                 *  自动用所调用的调度器ID所对应的视图文件进行渲染（如：[[Update]]用[[Edit]]进行映射后所对应的是`update`
                 *  视图文件而不是映射后的`edit`），如果需要用[[Edit]]调度器所对应的视图文件（如：`edit`）进行渲染，
                 *  则只需要显式配置`$view`参数即可，如：[[display('edit')]]
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
                            'themePath' => isset($dispatchOptions['themePath']) ? $dispatchOptions['themePath'] : null,
                        ]);
                        // 使用其他调度器映射
                        $route = $this->getUniqueId() . '/' . $this->service->normalizeDispatchName(isset($dispatchOptions['map'])
                                ? $dispatchOptions['map']
                                : $id
                            );
                        $classConfig = array_merge(['class' => $this->service->getNamespace($route)], $config);
                    }
                }  // 调度配置为类名
                elseif (class_exists($config)) {
                    $classConfig = $config;
                } // 其他字符串则为直接调度器映射
                else {
                    $this->_setDispatchOptions([
                        'theme' => $controller->dispatchTheme,
                    ]);
                    $route = $this->getUniqueId() . '/' . $this->service->normalizeDispatchName($config);
                    $classConfig = $this->service->getNamespace($route);
                }
            } // 调用系统默认调度器
            else {
                $this->_setDispatchOptions([
                    'theme' => $controller->dispatchTheme,
                ]);
                $route = $this->getUniqueId() . '/' . $this->service->normalizeDispatchName($id);
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
        // 是否自定义开发者主题基础路径，系统会调用该路径下指定路由的调度器
        if (isset($dispatchOptions['themePath']) && $dispatchOptions['themePath'] !== null) {
            $this->service->getView()->setBaseThemePath($dispatchOptions['themePath']);
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
    public function create($id, $classConfig, Controller $controller)
    {
        if (!is_array($classConfig)) {
            $classConfig = ['class' => $classConfig];
        }

        $className = $classConfig['class'];

        if (!class_exists($className)) {
            return null;
        } elseif (is_subclass_of($className, 'wocenter\core\Dispatch')) {
            $dispatch = Yii::createObject($classConfig, [
                $this->service->normalizeDispatchViewFileName($id),
                $controller
            ]);

            return get_class($dispatch) === $className ? $dispatch : null;
        } elseif (YII_DEBUG) {
            throw new InvalidConfigException("Dispatch class must extend from \\wocenter\\core\\Dispatch.");
        }

        return null;
    }

}
