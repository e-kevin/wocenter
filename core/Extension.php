<?php

namespace wocenter\core;

use wocenter\interfaces\ExtensionInterface;
use yii\base\{
    InvalidConfigException, Object
};

/**
 * 基础扩展实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Extension extends Object implements ExtensionInterface
{
    
    /**
     * @var string 扩展唯一ID，不可重复
     */
    private $_uniqueId;
    
    /**
     * @var string 所属应用
     */
    public $app;
    
    /**
     * @var string 扩展ID
     */
    public $id;
    
    /**
     * @var string 版本
     */
    public $version;
    
    /**
     * @var string 名称
     */
    public $name;
    
    /**
     * @var string 描述
     */
    public $description;
    
    /**
     * @var string 扩展网址
     */
    public $url;
    
    /**
     * @var string 开发者
     */
    public $developer = 'WoCenter';
    
    /**
     * @var string 开发者网站
     */
    public $webSite;
    
    /**
     * @var string 开发者邮箱
     */
    public $email = 'e-kevin@qq.com';
    
    /**
     * @var boolean 是否系统扩展
     */
    public $isSystem = false;
    
    /**
     * @var boolean 是否可安装
     */
    public $canInstall = false;
    
    /**
     * @var boolean 是否可卸载
     */
    public $canUninstall = false;
    
    /**
     * @inheritdoc
     */
    public function __construct($uniqueId, array $config = [])
    {
        $this->_uniqueId = $uniqueId;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->app == null) {
            throw new InvalidConfigException(get_called_class() . ': The "app" property must be set.');
        }
        if ($this->id == null) {
            throw new InvalidConfigException(get_called_class() . ': The "id" property must be set.');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function install()
    {
        if ($this->beforeInstall()) {
            $this->afterInstall();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 安装前执行
     */
    protected function beforeInstall()
    {
        return true;
    }
    
    /**
     * 安装后执行
     */
    protected function afterInstall()
    {
    }
    
    /**
     * @inheritdoc
     */
    public function uninstall()
    {
        if ($this->beforeUninstall()) {
            $this->afterUninstall();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 卸载前执行
     */
    protected function beforeUninstall()
    {
        return true;
    }
    
    /**
     * 卸载后执行
     */
    protected function afterUninstall()
    {
        $this->canUninstall = false;
    }
    
    /**
     * @inheritdoc
     */
    public function upgrade()
    {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function getUniqueId()
    {
        return $this->_uniqueId;
    }
    
}
