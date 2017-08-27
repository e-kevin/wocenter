<?php
namespace wocenter\console\controllers;

use wocenter\Wc;
use yii\console\controllers\MigrateController as BaseMigrateController;

class MigrateController extends BaseMigrateController
{

    /**
     * @inheritdoc
     */
    public $templateFile = '@wocenter/console/core/mysql/template.php';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // 添加已安装模块的数据库迁移目录
        $this->migrationPath = array_merge($this->migrationPath, Wc::$service->getModularity()->getLoad()->getMigrationPath());
    }

    public function actionInstall()
    {

    }

}
