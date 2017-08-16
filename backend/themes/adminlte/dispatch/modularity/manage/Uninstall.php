<?php
namespace wocenter\backend\themes\adminlte\dispatch\modularity\manage;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatch\modularity\manage
 */
class Uninstall extends Dispatch
{

    /**
     * @param string $id
     */
    public function run($id)
    {
        $model = Wc::$service->getModularity()->getModuleInfo($id);
        if ($model->infoInstance->canUninstall) {
            if ($model->delete()) {
                // 调用模块内置卸载方法
                $model->infoInstance->uninstall();
                $this->success('卸载成功', parent::RELOAD_LIST);
            } else {
                $this->error('卸载失败');
            }
        } else {
            $this->error($id . ' 模块属于系统模块，暂不支持卸载');
        }
    }

}
