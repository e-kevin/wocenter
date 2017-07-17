<?php
namespace wocenter\libs;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * 树形列表类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Tree
{

    /**
     * 获取指定模型指定主键的所有父级ID数据
     *
     * @param ActiveRecord|string $modelClass 模型对象或模型命名空间
     * @param integer $pk 待查询的主键值，仅在`$modelClass`为字符串时生效，否则会自动从`$modelClass`对象处获取
     * @param string $pkField 主键字段
     * @param string $parentField 父级字段
     * @param integer $rootId 顶级ID，默认为`0`，返回的父级ID数据获取到此顶级ID后则停止获取
     *
     * @return array
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public static function getParentIds($modelClass, $pk = 0, $pkField = 'id', $parentField = 'parent_id', $rootId = 0)
    {
        $model = '';
        if ($isObj = ($modelClass instanceof ActiveRecord)) {
            $model = $modelClass;
            if (!isset($model->$pkField)) {
                throw new InvalidConfigException("The `{$pkField}` property of model class: `{$modelClass::className()}` does not exists");
            }
            $pk = $model->$pkField;
        } elseif (!class_exists($modelClass)) {
            throw new InvalidConfigException("The model class: `{$modelClass}` does not exists");
        }
        if (empty($pk)) {
            $method = __METHOD__ . '()';
            throw new NotFoundHttpException("{$modelClass::className()}()必须先通过`{$pkField}`主键获取相关数据后才能执行操作：{$method}");
        }

        if (!$isObj) {
            $model = $modelClass::findOne([$pkField => $pk]);
        }
        if (!isset($model->$parentField)) {
            throw new InvalidConfigException("The `{$parentField}` property of model class: `{$modelClass::className()}` does not exists");
        }

        $_parentIds = [];
        while ($model !== null) {
            if ($model->$parentField !== $rootId) {
                array_unshift($_parentIds, $model->$parentField);
                $model = $modelClass::findOne([$pkField => $model->$parentField]);
            } else {
                $model = null;
            }
        }

        return $_parentIds;
    }

    /**
     * 获取指定模型指定主键的所有子类ID数据
     *
     * @param ActiveRecord|string $modelClass 模型对象或模型命名空间
     * @param integer $pk 待查询的主键值，仅在`$modelClass`为字符串时生效，否则会自动从`$modelClass`对象处获取
     * @param string $pkField 主键字段
     * @param string $parentField 父级字段
     *
     * @return array
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public static function getChildrenIds($modelClass, $pk = 0, $pkField = 'id', $parentField = 'parent_id')
    {
        if ($modelClass instanceof ActiveRecord) {
            if (!isset($modelClass->$pkField)) {
                throw new InvalidConfigException("The `{$pkField}` property of model class: `{$modelClass::className()}` does not exists");
            }
            $pk = $modelClass->$pkField;
        } elseif (!class_exists($modelClass)) {
            throw new InvalidConfigException("The model class: `{$modelClass}` does not exists");
        }
        if (empty($pk)) {
            $method = __METHOD__ . '()';
            throw new NotFoundHttpException("{$modelClass::className()}()必须先通过`{$pkField}`主键获取相关数据后才能执行操作：{$method}");
        }

        $_childrenIds = [];
        $children = $modelClass::findAll([$parentField => $pk]);
        while (count($children) > 0) {
            $first = array_shift($children);
            $_childrenIds[] = $first->$pkField;

            $next = $modelClass::findAll([$parentField => $first->$pkField]);
            if (count($next) > 0) {
                $children = array_merge($children, $next);
            }
        }

        return $_childrenIds;
    }

}
