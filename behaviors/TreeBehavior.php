<?php

namespace wocenter\behaviors;

use wocenter\{
    db\ActiveRecord, core\Model, helpers\TreeHelper
};
use Yii;
use yii\{
    base\Behavior, base\Component, base\InvalidConfigException, base\ModelEvent
};

/**
 * 树形数据行为类
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
    public $showTitleField = 'label';
    
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
            throw new InvalidConfigException('The owner of this behavior `' . self::class . '` must be instanceof ' .
                '`\wocenter\core\Model` or `\wocenter\db\ActiveRecord`');
        }
        parent::attach($owner);
    }
    
    /**
     * 获取树型下拉选项列表
     *
     * @param array $list 树形菜单数据
     *
     * @return array
     */
    public function getTreeSelectList(array $list): array
    {
        return $this->buildTreeOptions($this->_formatTree($list));
    }
    
    /**
     * 生成树型下拉选项数据
     *
     * @param array $list 菜单数据
     *
     * @return array
     */
    protected function buildTreeOptions($list)
    {
        $found = false;
        $options = [];
        /** @var self $model */
        $model = !empty($this->owner->{$this->pkField}) ? $this->owner : null;
        // 模型数据存在时，排除自身选项
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
    private function _formatTree($list, $level = 0)
    {
        $formatTree = [];
        foreach ($list as $val) {
            $tmp_str = str_repeat($this->tabSize, $level);
            $tmp_str .= "└&nbsp;";
            $val['level'] = $val['level'] ?? intval($level);
            $val[$this->showTitleField] = ($level ? $tmp_str : '') . $val[$this->showTitleField];
            if (!array_key_exists('items', $val)) {
                array_push($formatTree, $val);
            } else {
                $tmp_ary = $val['items'];
                unset($val['items']);
                array_push($formatTree, $val);
                $formatTree = array_merge($formatTree, $this->_formatTree($tmp_ary, $level + 1));
            }
        }
        
        return $formatTree;
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
     */
    public function getParentIds($rootId = 0)
    {
        if ($this->_parentIds === null) {
            $this->_parentIds = TreeHelper::getParentIds($this->owner, 0, $this->pkField, $this->parentField, $rootId);
        }
        
        return $this->_parentIds;
    }
    
    /**
     * @var array 子类ID数据
     */
    private $_childrenIds;
    
    /**
     * 获取当前模型所有子类ID数据
     *
     * @return array
     */
    public function getChildrenIds()
    {
        if ($this->_childrenIds === null) {
            $this->_childrenIds = TreeHelper::getChildrenIds($this->owner, $this->owner->{$this->pkField}, $this->pkField, $this->parentField);
        }
        
        return $this->_childrenIds;
    }
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'hasChildren',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'isValidParentId',
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
            $this->owner->_message = Yii::t('wocenter/app', 'Please delete or move the child data under this data before deleting it.');
            $event->isValid = false;
        }
    }
    
    /**
     * 检测当前模型的父级id是否有效，避免非法操作导致死循环
     *
     * @param ModelEvent $event
     */
    public function isValidParentId($event)
    {
        if (in_array($this->owner->{$this->parentField}, $this->getChildrenIds())) {
            $this->owner->_message = Yii::t('wocenter/app', 'Parent id is invalid.');
            $event->isValid = false;
        }
    }
    
}