<?php

namespace wocenter\helpers;

use wocenter\db\ActiveRecord;
use yii\{
    base\InvalidConfigException, web\NotFoundHttpException
};

/**
 * 树形列表数据助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class TreeHelper
{

    /**
     * 获取数据提供器所有父级ID数据
     *
     * @param ActiveRecord|array $dataProvider 数据提供器
     * @param integer $currentPid 当前数据的父级ID，从该值开始获取父级ID数据，直到$`rootId`值，
     * 该值仅在`$dataProvider`为数组类型时生效
     * @param string $pkField 主键字段
     * @param string $parentField 父级字段
     * @param integer $rootId 顶级ID，默认为`0`，返回的父级ID数据获取到此顶级ID后则停止获取
     *
     * @return array
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public static function getParentIds($dataProvider, int $currentPid = 0, $pkField = 'id', $parentField = 'parent_id', int $rootId = 0)
    {
        // 模型数据提供器
        if ($dataProvider instanceof ActiveRecord) {
            foreach ([$pkField, $parentField] as $field) {
                if (!isset($dataProvider->$field)) {
                    throw new InvalidConfigException("The `{$field}` property of model class: `{$dataProvider::className()}` does not exists");
                }
            }
            // 必须确保模型数据提供器存在有效数据
            if (empty($dataProvider->$pkField)) {
                $method = __METHOD__ . '()';
                throw new NotFoundHttpException("{$dataProvider::className()}()必须存在有效数据后才能执行操作：{$method}");
            }
            $currentPid = $dataProvider->$parentField;
            // 增加对模型内置[[getAll()]]方法的支持，可避免多次查询数据库，优化性能
            if ($dataProvider->hasMethod('getAll')) {
                $dataProvider = $dataProvider->getAll();
            }
        } // 数组数据提供器
        elseif (is_array($dataProvider)) {
        } else {
            throw new InvalidConfigException("The model class: `{$dataProvider}` does not exists");
        }
        
        if (0 === $currentPid) {
            return [];
        }
        
        if (is_array($dataProvider)) {
            return self::_getParentIdsByArray($dataProvider, $currentPid, $pkField, $parentField, $rootId);
        } else {
            return self::_getParentIdsByModel($dataProvider, $pkField, $parentField, $rootId);
        }
    }
    
    private static function _getParentIdsByArray(array $list, int $currentPid, string $pkField, string $parentField, int $rootId): array
    {
        $_parentIds = [];
        if ($list) {
            while ($currentPid !== $rootId) {
                array_unshift($_parentIds, $currentPid);
                $model = ArrayHelper::listSearch($list, [
                    $pkField => $currentPid,
                ]);
                $currentPid = $model ? (int)$model[0][$parentField] : $rootId;
            }
        }
        
        return $_parentIds;
    }
    
    private static function _getParentIdsByModel(ActiveRecord $model, string $pkField, string $parentField, int $rootId): array
    {
        $_parentIds = [];
        while ($model !== null) {
            if ($model->$parentField !== $rootId) {
                array_unshift($_parentIds, $model->$parentField);
                $model = $model::findOne([$pkField => $model->$parentField]);
            } else {
                $model = null;
            }
        }
        
        return $_parentIds;
    }
    
    /**
     * 获取数据提供器所有子类ID数据
     *
     * @param ActiveRecord|array $dataProvider 数据提供器
     * @param integer $currentId 获取该值的所有子类ID数据，为'0'时，表示获取所有顶级数据
     * @param string $pkField 主键字段
     * @param string $parentField 父级字段
     *
     * @return array
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public static function getChildrenIds($dataProvider, int $currentId = 0, $pkField = 'id', $parentField = 'parent_id')
    {
        // 模型数据提供器
        if ($dataProvider instanceof ActiveRecord) {
            foreach ([$pkField, $parentField] as $field) {
                if (!isset($dataProvider->$field)) {
                    throw new InvalidConfigException("The `{$field}` property of model class: `{$dataProvider::className()}` does not exists");
                }
            }
            // 必须确保模型数据提供器存在有效数据
            if (empty($dataProvider->$pkField)) {
                $method = __METHOD__ . '()';
                throw new NotFoundHttpException("{$dataProvider::className()}()必须存在有效数据后才能执行操作：{$method}");
            }
            // 增加对模型内置[[getAll()]]方法的支持，可避免多次查询数据库，优化性能
            if ($dataProvider->hasMethod('getAll')) {
                $dataProvider = $dataProvider->getAll();
            }
        } // 数组数据提供器
        elseif (is_array($dataProvider)) {
        } else {
            throw new InvalidConfigException("The model class: `{$dataProvider}` does not exists");
        }
        
        if (is_array($dataProvider)) {
            return self::_getChildrenIdsByArray($dataProvider, $currentId, $pkField, $parentField);
        } else {
            return self::_getChildrenIdsByModel($dataProvider, $currentId, $pkField, $parentField);
        }
    }
    
    private static function _getChildrenIdsByArray(array $list, int $currentId, string $pkField, string $parentField): array
    {
        $_childrenIds = [];
        if ($list) {
            $children = ArrayHelper::listSearch($list, [
                $parentField => $currentId,
            ]);
            while (count($children) > 0) {
                $first = array_shift($children);
                $_childrenIds[] = (int)$first[$pkField];
                $next = ArrayHelper::listSearch($list, [
                    $parentField => $first[$pkField],
                ]);
                if (count($next) > 0) {
                    $children = array_merge($children, $next);
                }
            }
        }
        
        return $_childrenIds;
    }
    
    private static function _getChildrenIdsByModel(ActiveRecord $model, int $currentId, string $pkField, string $parentField): array
    {
        $_childrenIds = [];
        $children = $model::findAll([$parentField => $currentId]);
        while (count($children) > 0) {
            $first = array_shift($children);
            $_childrenIds[] = $first->$pkField;
            $next = $model::findAll([$parentField => $first->$pkField]);
            if (count($next) > 0) {
                $children = array_merge($children, $next);
            }
        }
        
        return $_childrenIds;
    }
    
}
