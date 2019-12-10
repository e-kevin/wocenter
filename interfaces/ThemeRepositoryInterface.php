<?php

namespace wocenter\interfaces;

/**
 * 主题扩展仓库接口类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ThemeRepositoryInterface
{
    
    /**
     * 获取当前主题名
     *
     * @return array|null
     */
    public function getCurrentTheme();
    
    /**
     * 获取所有激活的主题
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getActiveTheme();
    
}
