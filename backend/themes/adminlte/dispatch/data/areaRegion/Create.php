<?php
namespace wocenter\backend\themes\adminlte\dispatch\data\areaRegion;

use wocenter\backend\modules\data\models\AreaRegion;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatch\data\areaRegion
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new AreaRegion();
        $pid = $this->_params[$model->breadcrumbParentParam];
        $request = Yii::$app->getRequest();

        $model->loadDefaultValues();
        $model->parent_id = $pid;

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, [
                    "/{$this->controller->getUniqueId()}",
                    $model->breadcrumbParentParam => $model->parent_id ?: null,
                ]);
            } else {
                $this->error($model->message);
            }
        }

        $breadcrumbs = $model->getBreadcrumbs($pid, '区域管理', '/data/area-region', [], [], ['新增区域']);

        return $this->assign([
            'model' => $model,
            'areaSelectList' => $model->getTreeSelectList($model->getAll()),
            'breadcrumbs' => $breadcrumbs,
            'title' => end($breadcrumbs),
        ])->display();
    }

}