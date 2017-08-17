<?php
namespace wocenter\core;

use wocenter\helpers\FileHelper;
use wocenter\Wc;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * 系统核心服务定位器，包含系统所有核心服务
 *
 * 主要为IDE提供友好支持，也可直接\Yii::$app->get('modularityService')方式调用，但显然建立该文件相当高效便捷
 *
 * @property \wocenter\services\AccountService $account
 * @property \wocenter\services\ActionService $action
 * @property \wocenter\services\DispatchService $dispatch
 * @property \wocenter\services\LogService $log
 * @property \wocenter\services\MenuService $menu
 * @property \wocenter\services\ModularityService $modularity
 * @property \wocenter\services\NotificationService $notification
 * @property \wocenter\services\PassportService $passport
 * @property \wocenter\services\SystemService $system
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ServiceLocator extends Object
{

    /**
     * 缓存所有服务类配置信息
     */
    const CACHE_ALL_SERVICE_CONFIG = 'allServiceConfig';

    /**
     * 如果需要自定义该值，可通过修改系统配置文件进行更改
     * 如在main.php(yii_advanced高级模板)或web.php(yii_basic基础模板)配置文件里添加以下配置：
     * 'container' => [
     *      'definitions' => [
     *          'wocenter\core\ServiceLocator' => [
     *              'serviceNamespace' => 'app\services\\',
     *          ],
     *      ]
     *  ],
     *
     * @var string 服务类命名空间
     */
    public $serviceNamespace = 'wocenter\\services';

    /**
     * @var Service[] 已经实例化的服务单例
     */
    private $_service;

    /**
     * 根据`$serviceName`实时加载系统服务类
     *
     * @param string $serviceName 服务名，不带后缀`Service`
     *
     * @return Service[]
     * @throws InvalidConfigException
     */
    public function getService($serviceName)
    {
        if (!isset($this->_service[$serviceName])) {
            $service = $serviceName . 'Service';
            // todo 如果存在自定义配置信息却不存在'class'键名,则提取系统默认的'class'值
            if (!Yii::$app->has($service)) {
                // todo 需要isset()判断
                Yii::$app->set($service, $this->loadServiceConfig()[$service]);
                unset($this->_allServices[$service]);

                return $this->getService($serviceName);
            }

            Yii::trace('Loading service: ' . $serviceName, __METHOD__);

            $this->_service[$serviceName] = Yii::$app->get($service, false);
            if (!$this->_service[$serviceName] instanceof Service) {
                throw new InvalidConfigException("The required service component `{$service}` must return an object extends `\\wocenter\\core\\Service`.");
            }
        }

        return $this->_service[$serviceName];
    }

    /**
     * 根据服务类命名空间自动获取服务目录
     *
     * @return bool|string
     */
    public function getServicePath()
    {
        return FileHelper::normalizePath(Yii::getAlias('@' . str_replace('\\', '/', $this->serviceNamespace)));
    }

    /**
     * @var array 系统所有服务的配置信息
     */
    private $_allServices = null;

    /**
     * 获取系统服务配置信息
     *
     * @return array
     */
    public function loadServiceConfig()
    {
        if ($this->_allServices !== null) {
            return $this->_allServices;
        }
        $this->_allServices = Wc::getOrSet(self::CACHE_ALL_SERVICE_CONFIG, function () {
            $allServices = [];
            $servicePath = $this->getServicePath();
            /**
             * 返回格式如下
             * $serviceFiles = [
             *  'wocenter/services/PassportService.php', // 父级服务
             *  'wocenter/services/passport/UcenterService.php', // 单层子服务
             * ]
             */
            $serviceFiles = FileHelper::findFiles($servicePath, [
                'except' => ['events', 'messages'],
                'only' => ['*Service.php'], // 字符串长度为 11
            ]);
            $path = str_replace('\\', DIRECTORY_SEPARATOR, $this->serviceNamespace);
            foreach ($serviceFiles as $file) {
                $file = substr($file, strpos($file, $path) + strlen($path) + 1, -11);
                // 存在子服务
                if (strpos($file, DIRECTORY_SEPARATOR) !== false) {
                    list($parent, $serviceName) = explode(DIRECTORY_SEPARATOR, $file, 2);
                    // 子服务存在子服务
                    if (strpos($serviceName, DIRECTORY_SEPARATOR) !== false) {
                        // 暂不支持多层级子服务
                        continue;
                    } else {
                        /**
                         * e.g.
                         * $serviceClass['passportService'] = [
                         *  'class' => 'wocenter\services\PassportService', // 父级服务
                         *  'subService' => [
                         *      'ucenter' => 'wocenter\services\passport\UcenterService', // 单层子服务
                         *  ]
                         * ]
                         */
                        $parentServiceName = $parent . 'Service';
                        $config = [
                            'class' => $this->serviceNamespace . '\\' .Inflector::camelize($parentServiceName),
                            'subService' => [
                                strtolower($serviceName) => [
                                    'class' => $this->serviceNamespace . '\\' .$parent . '\\' . $serviceName . 'Service',
                                ],
                            ],
                        ];
                        // 如果存在父级服务，则合并添加子服务，否则直接添加服务配置信息
                        if (isset($allServices[$parentServiceName])) {
                            $allServices[$parentServiceName] = ArrayHelper::merge(
                                $allServices[$parentServiceName],
                                $config
                            );
                        } else {
                            $allServices[$parentServiceName] = $config;
                        }
                    }
                } else {
                    $serviceName = $file . 'Service';
                    if (isset($allServices[Inflector::variablize($serviceName)])) {
                        continue;
                    }
                    $allServices[Inflector::variablize($serviceName)] = [
                        'class' => $this->serviceNamespace . '\\' .$serviceName,
                    ];
                }
            }

            return $allServices;
        }, false);

        return $this->_allServices;
    }

    /**
     * 用户管理服务类
     *
     * @return \wocenter\services\AccountService
     */
    public function getAccount()
    {
        return $this->getService('account');
    }

    /**
     * 模块管理服务类
     *
     * @return \wocenter\services\ModularityService
     */
    public function getModularity()
    {
        return $this->getService('modularity');
    }

    /**
     * 系统服务类
     *
     * @return \wocenter\services\SystemService
     */
    public function getSystem()
    {
        return $this->getService('system');
    }

    /**
     * 行为管理服务类
     *
     * @return \wocenter\services\ActionService
     */
    public function getAction()
    {
        return $this->getService('action');
    }

    /**
     * 日志管理服务类
     *
     * @return \wocenter\services\LogService
     */
    public function getLog()
    {
        return $this->getService('log');
    }

    /**
     * 调度服务类
     *
     * @return \wocenter\services\DispatchService
     */
    public function getDispatch()
    {
        return $this->getService('dispatch');
    }

    /**
     * 系统通知服务类
     *
     * @return \wocenter\services\NotificationService
     */
    public function getNotification()
    {
        return $this->getService('notification');
    }

    /**
     * 系统通行证服务类
     *
     * @return \wocenter\services\PassportService
     */
    public function getPassport()
    {
        return $this->getService('passport');
    }

    /**
     * 菜单管理服务类
     *
     * @return \wocenter\services\MenuService
     */
    public function getMenu()
    {
        return $this->getService('menu');
    }

}
