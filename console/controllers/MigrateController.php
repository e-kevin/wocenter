<?php
namespace wocenter\console\controllers;

use wocenter\helpers\FileHelper;
use wocenter\Wc;
use Yii;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\helpers\Console;

/**
 * Class MigrateController
 * WoCenter专用migrate操作类
 *
 * @package wocenter\console\controllers
 */
class MigrateController extends BaseMigrateController
{

    /**
     * @inheritdoc
     */
    public $templateFile = '@wocenter/console/template.php';

    /**
     * @var string 安装锁定文件
     */
    public $installLockFile = '@common/install.lock';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // 添加已安装模块的数据库迁移目录
        $this->migrationPath = array_merge($this->migrationPath, Wc::$service->getModularity()->getLoad()->getMigrationPath());
    }

    /**
     * 安装 WoCenter 高级项目模板应用
     *
     * @return int
     */
    public function actionInstall()
    {
        $installLockFile = Yii::getAlias($this->installLockFile);
        if (is_file($installLockFile)) {
            $this->stdout("====== 安装成功，请不要重复安装 ======\n", Console::FG_YELLOW);

            return self::EXIT_CODE_NORMAL;
        }

        if ($this->installMigration() == self::EXIT_CODE_ERROR) {
            return self::EXIT_CODE_ERROR;
        }

        $this->syncMenus();

        $this->stdout("====== 安装成功，欢迎使用 WoCenter ======\n\n", Console::FG_BLUE);

        FileHelper::createFile($installLockFile, 'lock');

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 安装数据库迁移
     *
     * @return int
     */
    protected function installMigration()
    {
        $this->stdout("====== 更新数据库 ======\n\n", Console::FG_YELLOW);

        $migrations = $this->getNewMigrations();
        if (empty($migrations)) {
            $this->stdout("您的数据库处于最新状态。\n\n", Console::FG_GREEN);
        } else {
            $total = count($migrations);
            $this->stdout("共有 $total 个更新可被安装:\n", Console::FG_YELLOW);

            foreach ($migrations as $migration) {
                $this->stdout("\t$migration\n");
            }
            $this->stdout("\n");

            $applied = 0;
            if ($this->confirm('确认安装以上更新？', true)) {
                foreach ($migrations as $migration) {
                    if (!$this->migrateUp($migration)) {
                        $this->stdout("\n共有 $applied 个更新已经完成。", Console::FG_RED);
                        $this->stdout("\n共有 " . ($total - $applied) . " 个更新失败。\n", Console::FG_RED);

                        $this->stdout("\n只有更新全部完成才可继续执行剩余步骤【同步模块菜单数据】。\n\n", Console::FG_GREEN);

                        return self::EXIT_CODE_ERROR;
                    }
                    $applied++;
                }

                $this->stdout("\n所有更新已经完成。\n\n", Console::FG_GREEN);
            }
        }
    }

    /**
     * 同步模块菜单数据
     */
    protected function syncMenus()
    {
        $this->stdout("====== 同步菜单数据 ======\n\n", Console::FG_YELLOW);

        $oldAppId = Yii::$app->id;
        Yii::$app->id = 'backend';
        if (Wc::$service->getMenu()->syncMenus()) {
            $this->stdout("菜单同步完成。\n\n", Console::FG_GREEN);
        } else {
            $this->stdout("菜单同步失败。\n\n", Console::FG_RED);
        }
        Yii::$app->id = $oldAppId;
    }

}
