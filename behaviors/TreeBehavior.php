<?php

namespace wocenter\behaviors;

use wocenter\{
    core\ActiveRecord, core\Model, helpers\ArrayHelper, libs\Tree
};
use yii\{
    base\Behavior, base\Component, base\InvalidConfigException, base\ModelEvent, web\NotFoundHttpException
};

/**
 * 树形菜单行为类
 *
 * @property integer $level 菜单层级
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class TreeBehavior extends Behavior
{
    
    /**
     * @var Model|ActiveRecord|Component|null
     */
    public $owner;
    
    /**
     * @var string 下拉选项需要显示的标题字段
     */
    public $showTitleField = 'name';
    
    /**
     * @var string 下拉选项的值字段
     */
    public $showPkField = 'id';
    
    /**
     * @var string 菜单主键字段，用于获取相关父级或子级菜单数据，通常是当前模型类的主键字段
     */
    public $pkField = 'id';
    
    /**
     * @var string 菜单父级字段
     */
    public $parentField = 'parent_id';
    
    /**
     * @var string 面包屑url父级参数名
     */
    public $breadcrumbParentParam = 'pid';
    
    /**
     * @var string TAB大小
     */
    public $tabSize = "&nbsp;&nbsp;&nbsp;&nbsp;";
    
    /**
     * @var integer 菜单层级
     */
    private $_level;
    
    /**
     * 获取菜单层级
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->_level;
    }
    
    /**
     * 设置菜单层级
     *
     * @param integer $value 菜单层级
     */
    public function setLevel($value)
    {
        $this->_level = $value;
    }
    
    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        if (!($owner instanceof Model || $owner instanceof ActiveRecord)) {
            throw new InvalidConfigException("The owner of this behavior `{$this->className()}` must be instanceof " .
                '`\wocenter\core\Model` or `\wocenter\core\ActiveRecord`');
        }
        parent::attach($owner);
    }
    
    /**
     * 获取树型下拉菜单选项列表
     *
     * @param array $list 菜单数据
     * @param integer $root 顶级菜单数值，默认为`0`，表示显示到最顶级菜单
     *
     * @return array
     */
    public function getTreeSelectList(array $list, $root = 0)
    {
        $list = ArrayHelper::listToTree($list, $this->pkField, $this->parentField, '_child', $root);
        $list = $this->_toFormatTree($list, 0);
        
        return $this->buildTreeOptionsToArray($list);
    }
    
    /**
     * 生成树型下拉选项数据
     *
     * @param array $list 菜单数据
     *
     * @return array
     */
    protected function buildTreeOptionsToArray($list)
    {
        $found = false;
        $options = [];
        // 模型数据存在时，排除自身选项
        /** @var self $model */
        $model = !empty($this->owner->{$this->pkField}) ? $this->owner : null;
        foreach ($list as $row) {
            if ($model != null) {
                // 排除自身选项
                if ($model[$this->showPkField] == $row[$this->showPkField]) {
                    $model->level = $row['level'];
                    $found = true;
                    continue;
                }
                // 排除归属自身的子类选项
                if ($found) {
                    if ($row['level'] > $model->level) {
                        continue;
                    } else {
                        $found = false;
                    }
                }
            }
            
            $options[$row[$this->showPkField]] = $row[$this->showTitleField];
        }
        
        return $options;
    }
    
    /**
     * 格式化菜单列表数据
     *
     * @param array $list
     * @param integer $level
     *
     * @return array
     */
    private function _toFormatTree($list, $level = 0)
    {
        $formatTree = [];
        foreach ($list as $val) {
            $tmp_str = str_repeat($this->tabSize, $level);
            $tmp_str .= "└&nbsp;";
            
            $val['level'] = intval($level);
            $val[$this->showTitleField] = $level == 0 ? $val[$this->showTitleField] : $tmp_str . $val[$this->showTitleField];
            if (!array_key_exists('_child', $val)) {
                array_push($formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($formatTree, $val);
                $formatTree = array_merge($formatTree, $this->_toFormatTree($tmp_ary, $level + 1));
            }
        }
        
        return $formatTree;
    }
    
    /**
     * 获取面包屑导航
     *
     * @param int $currentPid 当前父级ID
     * @param string $defaultLabel 默认的顶级面包屑标题
     * @param string $url 面包屑基础url
     * @param array $urlParams 面包屑url参数
     * @param array $appendToTop 自定义添加面包屑在所有数据之前
     * @param array $append 添加自定义面包屑在已有面包屑后面
     *
     * @return array ['label', 'url']
     */
    public function getBreadcrumbs($currentPid = 0, $defaultLabel = '列表', $url = '', $urlParams = [], $appendToTop = [], $append = [])
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = $this->owner;
        // 顶级面包屑
        $breadcrumbs[] = $defaultLabel;
        // 非顶级数据则获取该值的所有父级数据
        if (!empty($currentPid)) {
            if ($model->{$this->pkField} == $currentPid) {
                $self = $model;
            } else {
                /** @var self $self 当前`$currentPid`的模型数据 */
                $self = $model::find()->select([
                    $this->pkField,
                    $this->showTitleField,
                    $this->parentField,
                ])->where([$this->pkField => $currentPid])->one();
            }
            
            // 当前模型数据的所有父级数据
            $parentIds = $self->getParentIds();
            if (!empty($parentIds)) {
                $parents = $model::find()->select([
                    $this->pkField,
                    $this->showTitleField,
                    $this->parentField,
                ])->where([
                    $this->pkField => $parentIds,
                ])->asArray()->all();
                $parents = ArrayHelper::listToTree($parents, $this->pkField, $this->parentField, '_child');
                // 所有的父级面包屑
                $breadcrumbs = ArrayHelper::merge($breadcrumbs, $this->_toFormatBreadcrumbs($parents));
            }
            // 包含自身面包屑
            $breadcrumbs[$self->{$this->pkField}] = $self->{$this->showTitleField};
        }
        
        // 格式化面包屑
        foreach ($breadcrumbs as $id => &$title) {
            $arr = [];
            $arr['label'] = $title;
            if ($currentPid != $id || $append) {
                $arr['url'][] = $url;
                $arr['url'][$this->breadcrumbParentParam] = $id ?: null;
                if ($urlParams) {
                    $arr['url'] = ArrayHelper::merge($urlParams, $arr['url']);
                }
            }
            $title = $arr;
        }
        
        // 添加自定义面包屑在已有面包屑后面
        if ($append) {
            $breadcrumbs = ArrayHelper::merge($breadcrumbs, $append);
        }
        
        // 添加自定义面包屑在所有面包屑前端
        if ($appendToTop) {
            $breadcrumbs = ArrayHelper::merge($appendToTop, $breadcrumbs);
        }
        
        return $breadcrumbs;
    }
    
    /**
     * 格式化面包屑导航数据
     *
     * @param array $list
     *
     * @return array
     */
    private function _toFormatBreadcrumbs($list)
    {
        $format = [];
        foreach ($list as $val) {
            $format[$val[$this->pkField]] = $val[$this->showTitleField];
            if (array_key_exists('_child', $val)) {
                $format = ArrayHelper::merge($format, $this->_toFormatBreadcrumbs($val['_child']));
            }
        }
        
        return $format;
    }
    
    /**
     * @var array 父级ID数据
     */
    private $_parentIds;
    
    /**
     * 获取当前模型所有父级ID数据
     *
     * @param integer $rootId 顶级ID，默认为`0`，返回的父级ID数据获取到此顶级ID后则停止获取
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function getParentIds($rootId = 0)
    {
        if ($this->_parentIds === null) {
            // 增加对模型内置[[getAll()]]方法的支持，可避免多次查询数据库，优化性能
            if ($this->owner->hasMethod('getAll')) {
                $this->_parentIds = $this->getParentIdsInternal($rootId);
            } else {
                $this->_parentIds = Tree::getParentIds($this->owner, 0, $this->pkField, $this->parentField, $rootId);
            }
        }
        
        return $this->_parentIds;
    }
    
    /**
     * 获取缓存内的所有父级ID数据
     *
     * @param integer $rootId 顶级ID，默认为`0`，返回的父级ID数据获取到此顶级ID后则停止获取
     *
     * @return array
     * @throws NotFoundHttpException
     */
    protected function getParentIdsInternal($rootId)
    {
        $model = $this->owner;
        if (empty($model->{$this->pkField})) {
            $method = __METHOD__ . '()';
            throw new NotFoundHttpException("{$model->className()}()必须先通过`{$this->pkField}`主键获取相关数据后才能执行操作：{$method}");
        }
        
        $_parentIds = [];
        $all = $this->owner->getAll();
        while (!empty($model)) {
            $model[$this->parentField] = (int)$model[$this->parentField];
            if ($model[$this->parentField] !== $rootId) {
                array_unshift($_parentIds, $model[$this->parentField]);
                $model = ArrayHelper::listSearch($all, [
                    $this->pkField => $model[$this->parentField],
                ]);
                $model = isset($model[0]) ? $model[0] : [];
            } else {
                $model = [];
            }
        }
        
        return $_parentIds;
    }
    
    /**
     * @var array 子类ID数据
     */
    private $_childrenIds;
    
    /**
     * 获取当前模型所有子类ID数据
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function getChildrenIds()
    {
        if ($this->_childrenIds === null) {
            // 增加对模型内置[[getAll()]]方法的支持，可避免多次查询数据库，优化性能
            if ($this->owner->hasMethod('getAll')) {
                $this->_childrenIds = $this->getChildrenIdsInternal();
            } else {
                $this->_childrenIds = Tree::getChildrenIds($this->owner, 0, $this->pkField, $this->parentField);
            }
        }
        
        return $this->_childrenIds;
    }
    
    /**
     * 获取缓存内的所有子类ID数据
     *
     * @return array
     * @throws NotFoundHttpException
     */
    protected function getChildrenIdsInternal()
    {
        if (empty($this->owner->{$this->pkField})) {
            $method = __METHOD__ . '()';
            throw new NotFoundHttpException("{$this->owner->className()}()必须先通过`{$this->pkField}`主键获取相关数据后才能执行操作：{$method}");
        }
        
        $_childrenIds = [];
        if (method_exists($this, 'getAll')) {
            $all = $this->owner->getAll();
            if ($all) {
                $children = ArrayHelper::listSearch($all, [
                    $this->parentField => $this->owner->{$this->pkField},
                ]);
                while (count($children) > 0) {
                    $first = array_shift($children);
                    $_childrenIds[] = (int)$first[$this->pkField];
                    
                    $next = ArrayHelper::listSearch($all, [
                        $this->parentField => $first[$this->pkField],
                    ]);
                    if (count($next) > 0) {
                        $children = array_merge($children, $next);
                    }
                }
            }
        }
        
        return $_childrenIds;
    }
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'hasChildren',
        ];
    }
    
    /**
     * 检测当前模型是否拥有子类数据
     *
     * @param ModelEvent $event
     */
    public function hasChildren($event)
    {
        if (!empty($this->getChildrenIds())) {
            $this->owner->message = '删除该数据前请删除或转移其下的子级数据';
            $event->isValid = false;
        }
    }
    
}