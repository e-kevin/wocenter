<?php
namespace wocenter\backend\themes\adminlte\widgets;

use Closure;
use rmrevin\yii\fontawesome\FA;
use wocenter\Wc;
use Yii;
use yii\helpers\Html;

/**
 * Class Menu
 * Theme menu widget.
 */
class Menu extends \wonail\adminlte\widgets\Menu
{

    /**
     * @var string 菜单数据里显示菜单名称的字段
     */
    public $labelField = 'alias_name';

    /**
     * @var string 菜单数据里二级菜单的键名
     */
    public $submenuName = '_child';

    /**
     * @var string 菜单数据里显示菜单图标的字段
     */
    public $iconField = 'icon_html';

    /**
     * @var string 开发模式可见字段
     */
    public $showOnDevelopField = 'is_dev';

    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     * The activated parent menu items will also have its CSS classes appended with [[activeCssClass]].
     */
    public $activateParents = true;

    /**
     * @inheritdoc
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items as $i => $item) {
            if (
                (isset($item['visible']) && !$item['visible']) ||
                // 是否开发模式可见
                (YII_ENV === 'dev' && isset($item[$this->showOnDevelopField]) && !$item[$this->showOnDevelopField])
            ) {
                unset($items[$i]);
                continue;
            }
            // 处理url地址
            $this->parseUrl($items[$i], $item);
            if (!isset($item[$this->labelField])) {
                $item[$this->labelField] = !empty($item['name']) ? $item['name'] : FA::i($this->submenuDefaultIcon);
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i][$this->labelField] = $encodeLabel ? Html::encode($item[$this->labelField]) : $item[$this->labelField];
            $hasActiveChild = false;
            if (isset($item[$this->submenuName])) {
                $items[$i][$this->submenuName] = $this->normalizeItems($item[$this->submenuName], $hasActiveChild);
                if (empty($items[$i][$this->submenuName]) && $this->hideEmptyItems) {
                    unset($items[$i][$this->submenuName]);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
            }
            if (!isset($item['active'])) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active'] instanceof Closure) {
                $active = $items[$i]['active'] = call_user_func($item['active'], $item, $hasActiveChild, $this->isItemActive($item), $this);
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    /**
     * @inheritdoc
     */
    protected function parseUrl(&$items, &$item)
    {
        // todo 添加对url参数[params]的支持
        $urlList = ['url', 'full_url'];
        foreach ($urlList as $url) {
            if (isset($item[$url])) {
                $urlValue = $item[$url];
                $items[$url] = $item[$url] = is_string($urlValue) && strpos($urlValue, '/') === 0 ? (array)$urlValue : $urlValue;
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        if (!isset($item['url'])) {
            return false;
        }

        // 用完整路由地址[full_url]和系统请求的路由地址[$this->route]做对比，而路由地址[url]则作为菜单地址显示，通常[url]都是简写地址
        if (isset($item['full_url'])) {
            $item['url'] = $item['full_url'];
        }

        return parent::isItemActive($item);
    }

}
