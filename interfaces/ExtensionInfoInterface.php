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
 * @property string $app 所属应用
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ExtensionInfoInterface
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
    const CATEGORY_NONE = 'category_none';
    
    /**
     * @var integer 首页控制器分类
     */
    const CATEGORY_SITE = 'category_site';
    
    /**
     * @var integer 系统模块分类
     */
    const CATEGORY_SYSTEM = 'category_system';
    
    /**
     * @var integer 扩展模块分类
     */
    const CATEGORY_EXTENSION = 'category_extension';
    
    /**
     * @var integer 菜单模块分类
     */
    const CATEGORY_MENU = 'category_menu';
    
    /**
     * @var integer 用户模块分类
     */
    const CATEGORY_ACCOUNT = 'category_account';
    
    /**
     * @var integer 通行证模块分类
     */
    const CATEGORY_PASSPORT = 'category_passport';
    
    /**
     * @var integer 安全模块分类
     */
    const CATEGORY_SECURITY = 'category_security';
    
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
    
    /**
     * 获取扩展配置信息允许的键名，用于过滤非法的配置数据
     *
     * @return array
     */
    public function getConfigKey();
    
    /**
     * 获取扩展配置信息
     * 可能包含的键名如下：
     * - `components`
     * - `params`
     * - `modules`
     * - `controllerMap`
     * 详情请查看[[getConfigKey()]]
     * @see getConfigKey()
     *
     * @return array
     */
    public function getConfig();
    
    /**
     * 获取扩展公共配置信息允许的键名，用于过滤非法的配置数据
     *
     * @return array
     */
    public function getCommonConfigKey();
    
    /**
     * 获取扩展公共配置信息
     * 可能包含的键名如下：
     * - `components`
     * - `params`
     * - `modules`
     * - `controllerMap`
     * 详情请查看[[getCommonConfigKey()]]
     * @see getCommonConfigKey()
     *
     * @return array
     */
    public function getCommonConfig();
    
}
