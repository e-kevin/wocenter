<?php
namespace wocenter\backend\themes\adminlte\assetBundle;

use yii\web\AssetBundle;

class SiteAsset extends AssetBundle
{

    public $sourcePath = '@wocenter/backend/themes/adminlte/assets';

    public $depends = [
        'diiimonn\assets\SlimScrollAsset',
        // 系统多个页面的搜索操作页面是通过Modal模态框加载实现的，添加该依赖可避免提交操作后页面样式经yii.activeForm.js验证被还原的问题
        'wonail\adminlte\assetBundle\Select2Asset',
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];

}
