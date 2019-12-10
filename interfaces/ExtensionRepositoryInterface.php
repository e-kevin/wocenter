<?php

namespace wocenter\interfaces;

use wocenter\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * 扩展仓库接口类
 *
 * @property ActiveRecord $info
 * @property array $all
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ExtensionRepositoryInterface
{
    
    /**
     * 获取扩展详情，主要用于管理和安装
     *
     * @param string $extensionName 扩展名称
     *
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function getInfo($extensionName);
    
    /**
     * 获取所有已经安装的扩展数据，并以扩展名为索引
     *
     * @return array
     * [
     *  {uniqueName} => [],
     * ]
     */
    public function getAll();
    
    /**
     * 获取扩展模型
     *
     * @return ActiveRecord
     */
    public function getModel();
    
    /**
     * 设置扩展模型
     *
     * @param null|string|array $config
     */
    public function setModel($config = []);
    
}
