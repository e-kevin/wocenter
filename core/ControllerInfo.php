<?php

namespace wocenter\core;

use wocenter\{
    interfaces\ControllerInfoInterface, traits\ExtensionTrait
};

/**
 * 基础控制器扩展信息类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ControllerInfo extends ExtensionInfo implements ControllerInfoInterface
{
    
    use ExtensionTrait;
    
    /**
     * @var string 扩展所属模块ID
     */
    protected $_moduleId;
    
    /**
     * @var string 数据库迁移路径
     */
    private $_migrationPath;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // 如果没有指定扩展所属模块，则默认为扩展所属的应用
        if (empty($this->getModuleId())) {
            $this->setModuleId($this->app);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function getModuleId()
    {
        return $this->_moduleId;
    }
    
    /**
     * @inheritdoc
     */
    public function setModuleId($_moduleId)
    {
        $this->_moduleId = $_moduleId;
    }
    
    /**
     * @inheritdoc
     */
    public function getMigrationPath()
    {
        return $this->_migrationPath;
    }
    
    /**
     * @inheritdoc
     */
    public function setMigrationPath($migrationPath)
    {
        $this->_migrationPath = $migrationPath;
    }
    
    /**
     * @var array 模块配置信息允许的键名
     */
    private $_configKey = ['components', 'params'];
    
    /**
     * @inheritdoc
     */
    public function getConfigKey()
    {
        return $this->_configKey;
    }
    
    /**
     * @inheritdoc
     */
    public function install()
    {
        if (false == parent::install()) {
            return false;
        }
        $this->runMigrate('up');
        
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function uninstall()
    {
        if (false == parent::uninstall()) {
            return false;
        }
        $this->runMigrate('down');
        
        return true;
    }
    
}
