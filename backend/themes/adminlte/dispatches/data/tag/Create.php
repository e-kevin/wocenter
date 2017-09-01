<?php
namespace wocenter\backend\themes\adminlte\dispatches\data\tag;

use wocenter\backend\modules\data\models\Tag;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 *
 * @package wocenter\backend\themes\adminlte\dispatches\data\tag
 */
class Create extends Dispatch
{

    /**
     * @param integer $pid
     *
     * @return string|\yii\web\Response
     */
    public function run($pid = 0)
    {
        $model = new Tag();
        $request = Yii::$app->getRequest();

        $model->loadDefaultValues();
        $model->parent_id = $pid;

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, [
                    "/{$this->controller->getUniqueId()}",
                    'pid' => $model->parent_id ?: null,
                ]);
            } else {
                $this->error($model->message);
            }
        }

        $breadcrumbs = $model->getBreadcrumbs($pid, '标签列表', '/data/tag/index', [], [], ['新增标签']);

        return $this->assign([
            'model' => $model,
            'tagList' => $model->getTreeSelectList($model->getList()),
            'breadcrumbs' => $breadcrumbs,
            'title' => end($breadcrumbs),
        ])->display();
    }

}
