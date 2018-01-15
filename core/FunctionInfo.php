<?php

namespace wocenter\core;

use wocenter\{
    interfaces\FunctionInfoInterface, traits\ExtensionTrait
};

/**
 * 基础功能扩展信息类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
abstract class FunctionInfo extends Extension implements FunctionInfoInterface
{
    
    use ExtensionTrait;
    
    /**
     * @var string 扩展所属模块ID
     */
    protected $moduleId;
    
    /**
     * @var string 数据库迁移路径
     */
    private $_migrationPath;
    
    /**
     * @inheritdoc
     */
    protected $mustBeSetProps = ['app', 'id', 'moduleId'];
    
    /**
     * @var array 模块配置信息允许的键名
     */
    private $_configKey = ['components', 'params'];
    
    /**
     * 获取扩展菜单信息
     *
     * @return array
     * @see \wocenter\core\ModularityInfo::getMenus()
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
        return $this->moduleId;
    }
    
    /**
     * @inheritdoc
     */
    public function setModuleId($moduleId)
    {
        $this->moduleId = $moduleId;
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
     * @inheritdoc
     */
    public function getConfigKey()
    {
        return $this->_configKey;
    }
    
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function install()
    {
        parent::install();
        $this->runMigrate('up');
        
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function uninstall()
    {
        parent::uninstall();
        $this->runMigrate('down');
        
        return true;
    }
    
}
