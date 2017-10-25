<?php

namespace wocenter\services\extension;

use wocenter\backend\modules\extension\models\Theme;
use wocenter\core\Service;
use wocenter\core\ThemeInfo;
use wocenter\services\ExtensionService;
use Yii;

/**
 * 主题管理子服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ThemeService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * @var string|array|callable|Theme 模块功能扩展类
     */
    public $themeModel = '\wocenter\backend\modules\extension\models\Theme';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'theme';
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        $this->service->getLoad()->clearCache();
    }
    
    /**
     * 获取本地所有主题扩展配置信息
     *
     * @return array
     * [
     *  {name} => [
     *      'infoInstance' => {infoInstance},
     *  ]
     * ]
     */
    public function getAllConfig()
    {
        return isset($this->service->getLoad()->getAllExtensionConfig()[Yii::$app->id]['themes'])
            ? $this->service->getLoad()->getAllExtensionConfig()[Yii::$app->id]['themes']
            : [];
    }
    
    /**
     * 获取当前系统主题详情信息
     *
     * @return ThemeInfo|null
     */
    public function getCurrentTheme()
    {
        /** @var Theme $model */
        $model = Yii::createObject($this->themeModel);
        // 已经安装的主题扩展
        $installed = $model->getInstalledThemes();
        // 所有主题扩展配置
        $allConfig = $this->getAllConfig();
        /** @var \wocenter\core\View $view */
        $view = Yii::$app->getView();
        foreach ($allConfig as $name => $row) {
            /** @var ThemeInfo $infoInstance */
            $infoInstance = $row['infoInstance'];
            // 剔除未激活的主题扩展
            if (!isset($installed[$infoInstance->getUniqueId()])
                || $infoInstance->id != $view->getThemeName()
            ) {
                continue;
            }
            
            return $infoInstance;
        }
        
        return null;
    }
    
}
