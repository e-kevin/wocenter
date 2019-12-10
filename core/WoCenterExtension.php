<?php

namespace wocenter\core;

use wocenter\interfaces\RunningExtensionInterface;
use wocenter\interfaces\ExtensionInfoInterface;
use wocenter\traits\DispatchTrait;
use wocenter\Wc;
use Yii;
use yii\base\BaseObject;
use yii\base\Controller;
use yii\helpers\Json;

/**
 * WoCenter扩展
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class WoCenterExtension extends BaseObject implements RunningExtensionInterface
{
    
    /**
     * 调用该类的控制器
     *
     * @var Controller|DispatchTrait
     */
    protected $controller;
    
    /**
     * @var string 当前控制器的上级命名空间
     */
    private $_namespace;
    
    /**
     * @inheritdoc
     */
    public function __construct(Controller $controller, array $config = [])
    {
        $this->controller = $controller;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_namespace = ($this->controller->module instanceof Modularity) ?
            $this->controller->module->baseNamespace :
            substr((new \ReflectionClass($this->controller))->getNamespaceName(), 0, -12);
    }
    
    /**
     * @inheritdoc
     */
    public function getNamespace(): string
    {
        return $this->_namespace;
    }
    
    /**
     * @inheritdoc
     */
    public function isExtensionController(): bool
    {
        return strpos($this->_namespace, 'extensions') === 0;
    }
    
    /**
     * @inheritdoc
     */
    public function getInfo()
    {
        return $this->defaultExtension();
    }
    
    /**
     * @inheritdoc
     */
    public function getDbConfig(): array
    {
        return [
            'run' => ExtensionInfoInterface::RUN_MODULE_EXTENSION,
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getExtensionUniqueName(): string
    {
        return $this->getInfo()->getUniqueName();
    }
    
    /**
     * @inheritdoc
     */
    public function defaultExtension()
    {
        $info = Json::decode(file_get_contents(Yii::getAlias('@wocenter/composer.json')));
        
        return Yii::createObject([
            'class' => ExtensionInfo::class,
            'app' => 'Application',
            'id' => 'wocenter',
            'repositoryUrl' => $info['homepage'],
            'name' => 'WoCenter核心',
            'description' => $info['description'],
            'developer' => $info['authors'],
        ], [
            'vendor/' . $info['name'],
            'vendor/' . $info['name'],
            $info['version'] ?? Wc::getVersion(),
        ]);
    }
    
}