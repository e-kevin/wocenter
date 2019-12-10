<?php

namespace wocenter\core;

use wocenter\interfaces\ModularityInfoInterface;
use wocenter\traits\ExtensionTrait;

/**
 * 基础模块信息实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ModularityInfo extends ExtensionInfo implements ModularityInfoInterface
{
    
    use ExtensionTrait;
    
    /**
     * @var boolean 是否启用bootstrap
     */
    public $bootstrap = false;
    
    /**
     * @var string 数据库迁移路径
     */
    private $_migrationPath;
    
    /**
     * @var array 模块配置信息允许的键名
     */
    private $_configKey = ['components', 'params', 'modules'];
    
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
    public function install()
    {
        if (parent::install() == false) {
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
        if (parent::uninstall() == false) {
            return false;
        }
        $this->runMigrate('down');
        
        return true;
    }
    
}
