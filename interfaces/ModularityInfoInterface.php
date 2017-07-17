<?php
namespace wocenter\interfaces;

/**
 * 模块详情接口类
 *
 * @package wocenter\interfaces
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ModularityInfoInterface
{

    /**
     * 模块菜单信息
     *
     * @return array
     */
    public function getMenus();

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
