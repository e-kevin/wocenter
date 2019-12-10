<?php

namespace wocenter\interfaces;

use wocenter\core\ExtensionInfo;
use wocenter\core\WoCenterExtension;
use yii\base\Controller;

/**
 * 当前控制器所属的扩展接口类
 *
 * @property string $namespace
 * @property array $info
 * @property array $dbConfig
 * @property string $extensionUniqueName
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface RunningExtensionInterface
{
    
    public function __construct(Controller $controller, array $config = []);
    
    /**
     * 当前控制器的上级命名空间
     *
     * @return string
     */
    public function getNamespace():string;
    
    /**
     * 是否属于扩展内的控制器
     *
     * @return bool
     */
    public function isExtensionController(): bool;
    
    /**
     * 控制器所属扩展的本地配置文件信息
     *
     * @return ExtensionInfo|object
     */
    public function getInfo();
    
    /**
     * 控制器所属扩展的数据库配置信息，目前主要是获取扩展当前的运行模式。
     * 数据库配置信息里必须包含以下字段：
     *  - `run`: 扩展运行模式
     *
     * @return array
     */
    public function getDbConfig(): array;
    
    /**
     * 获取当前控制器所属的扩展名称
     *
     * @return string
     */
    public function getExtensionUniqueName(): string;
    
    /**
     * 控制器不属于任何一个扩展时，可用该方法为控制器指定一个所属扩展
     * WoCenter提供了一个默认的扩展类@see WoCenterExtension
     *
     * @return self|object
     */
    public function defaultExtension();
    
}