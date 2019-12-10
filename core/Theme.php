<?php

namespace wocenter\core;

use Yii;

/**
 * 支持系统调度功能的主题组件
 *
 * @property array $themeConfig
 * @property array $defaultPathMap
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Theme extends \yii\base\Theme
{
    
    public function __construct($config = [])
    {
        if (!isset($config['pathMap'])) {
            $this->pathMap = $this->getDefaultPathMap();
        }
        parent::__construct($config);
    }
    
    /**
     * 获取当前主题的参数配置
     * - string 'name': 当前主题的ID
     * - string 'dispatch': 当前主题的公共调度器行为类
     * - string|array 'viewPath': 当前主题的视图目录地址，支持数组格式映射
     * @example
     * ```php
     *      'viewPath' => [
     *          '@app/themes/basic',
     *          '@app/themes/adminlte',
     *      ]
     * ```
     * @see ThemeInfo::getConfig()
     *
     * @return array
     */
    public function getThemeConfig(): array
    {
        return Yii::$app->params['themeConfig'] ?? [
                'name' => 'basic',
                'dispatch' => '\wocenter\behaviors\DispatchBehavior',
                'viewPath' => '@app/themes/basic',
            ];
    }
    
    /**
     * 获取默认的视图路径映射
     *
     * @return array
     */
    public function getDefaultPathMap(): array
    {
        $pathMap = [
            '@developer' => [
                '@developer',
                '@extensions',
            ],
            '@extensions' => [
                '@developer', // 只要开发者目录内存在系统扩展里相应的视图文件，即可优先被渲染
                '@extensions',
            ],
        ];
        // 添加对'@app/views'视图路径的映射
        foreach ((array) $this->getThemeConfig()['viewPath'] as $key => $value) {
            $pathMap['@app/views'][$key] = $value;
        }
        
        return $pathMap;
    }
    
}
