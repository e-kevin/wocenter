<?php
namespace wocenter\backend\core;

use yii\base\Module;

/**
 * 后台基础Modularity类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Modularity extends Module
{

    /**
     * @var array 后台系统核心模块，必须安装的
     */
    public static $coreModule = ['modularity', 'system', 'core', 'data'];

}
