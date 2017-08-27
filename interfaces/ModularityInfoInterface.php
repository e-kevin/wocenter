<?php
namespace wocenter\interfaces;

/**
 * 模块详情接口类
 *
 * @property string $id 模块唯一标识，只读属性
 * @property array $menus 模块菜单信息，只读属性
 * @property array $urlRule 模块路由规则，只读属性
 *
 * @package wocenter\interfaces
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ModularityInfoInterface
{

    /**
     * 获取模块唯一标识
     *
     * @return string
     */
    public function getId();

    /**
     * 获取模块菜单信息
     *
     * @return array
     */
    public function getMenus();

    /**
     * 获取模块路由规则
     *
     * @return array
     */
    public function getUrlRules();

    /**
     * 安装模块
     */
    public function install();

    /**
     * 卸载模块
     */
    public function uninstall();

    /**
     * 模块升级
     */
    public function upgrade();

}
