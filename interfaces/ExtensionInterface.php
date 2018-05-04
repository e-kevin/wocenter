<?php

namespace wocenter\interfaces;

/**
 * 扩展接口类
 *
 * @property string $uniqueId 扩展唯一ID，只读属性
 * @property string $uniqueName 扩展唯一名称，不可重复
 * @property string $version 版本
 * @property array $depends 扩展所需依赖
 * @property array $requirePackages 扩展所需的composer包
 * @property array $category 获取扩展所属类型
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ExtensionInterface
{
    
    /**
     * @var integer 运行系统扩展
     */
    const RUN_MODULE_EXTENSION = 0;
    
    /**
     * @var integer 运行开发者扩展
     */
    const RUN_MODULE_DEVELOPER = 1;
    
    /**
     * @var integer 默认扩展不属于任何分类
     */
    const CATEGORY_NONE = 0;
    
    /**
     * @var integer 首页控制器分类
     */
    const CATEGORY_SITE = 1;
    
    /**
     * @var integer 系统模块分类
     */
    const CATEGORY_SYSTEM = 2;
    
    /**
     * @var integer 扩展模块分类
     */
    const CATEGORY_EXTENSION = 3;
    
    /**
     * @var integer 菜单模块分类
     */
    const CATEGORY_MENU = 4;
    
    /**
     * @var integer 用户模块分类
     */
    const CATEGORY_ACCOUNT = 5;
    
    /**
     * @var integer 通行证模块分类
     */
    const CATEGORY_PASSPORT = 6;
    
    /**
     * @var integer 安全模块分类
     */
    const CATEGORY_SECURITY = 7;
    
    /**
     * 获取扩展唯一ID，不可重复
     *
     * @return string
     */
    public function getUniqueId();
    
    /**
     * 获取扩展唯一名称，不可重复
     *
     * @return string
     */
    public function getUniqueName();
    
    /**
     * 安装
     *
     * @return boolean
     */
    public function install();
    
    /**
     * 卸载
     *
     * @return boolean
     */
    public function uninstall();
    
    /**
     * 升级
     *
     * @return boolean
     */
    public function upgrade();
    
    /**
     * 版本
     *
     * @return string
     */
    public function getVersion();
    
    /**
     * 获取扩展所需依赖
     *
     * @return array
     */
    public function getDepends();
    
    /**
     * 获取扩展所需的composer包
     *
     * @return array
     */
    public function getRequirePackages();
    
    /**
     * 获取扩展所属类型
     *
     * @return string|null
     */
    public function getCategory();
    
}
