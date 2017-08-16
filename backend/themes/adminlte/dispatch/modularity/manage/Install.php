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
class Install extends Dispatch
{

    /**
     * @param string $id
     *
     * @return string|\yii\web\Response
     */
    public function run($id)
    {
        $model = Wc::$service->getModularity()->getModuleInfo($id, false);
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            $model->load($request->getBodyParams());
            // 是否为系统模块，以模块配置信息为准
            $model->is_system = $model->infoInstance->isSystem ?: $model->is_system;
            // 调用模块内置安装方法
            $model->infoInstance->install();
            if ($model->save(false)) {
                $this->success('安装成功', ["/{$this->controller->module->id}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
        ])->display();
    }

}
