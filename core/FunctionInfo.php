<?php

namespace wocenter\core;

use wocenter\{
    helpers\FileHelper, helpers\WebConsoleHelper, interfaces\FunctionInfoInterface, Wc
};
use Yii;
use yii\base\{
    InvalidConfigException, InvalidParamException
};

/**
 * 基础功能扩展信息类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class FunctionInfo extends Extension implements FunctionInfoInterface
{
    
    /**
     * @var string 扩展所属模块ID
     */
    public $moduleId;
    
    /**
     * @var string 数据库迁移路径
     */
    private $_migrationPath;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_null($this->moduleId)) {
            throw new InvalidConfigException(get_called_class() . ': The "moduleId" property must be set.');
        }
        parent::init();
    }
    
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
    public function getUrlRules()
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
    public function beforeInstall()
    {
        if (parent::beforeInstall()) {
            
            $this->runMigrate('up');
            
            $this->injectMenus();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 执行migrate操作
     *
     * @param string $type 操作类型
     */
    protected function runMigrate($type)
    {
        if (FileHelper::isDir(Yii::getAlias($this->migrationPath))) {
            $action = "migrate/";
            switch ($type) {
                case 'up':
                    $action .= 'up';
                    break;
                case 'down':
                    $action .= 'down';
                    break;
                default:
                    throw new InvalidParamException('The "type" property is invalid.');
            }
            $cmd = "%s {$action} --migrationPath=%s --interactive=0";
            //执行
            WebConsoleHelper::run(sprintf($cmd,
                Yii::getAlias(WebConsoleHelper::getYiiCommand()),
                Yii::getAlias($this->migrationPath)
            ));
        }
    }
    
    /**
     * 插入功能扩展菜单
     */
    protected function injectMenus()
    {
        $menus = Wc::$service->getMenu()->formatMenuConfig($this->getMenus());
        Wc::$service->getMenu()->syncMenus($menus);
    }
    
}
