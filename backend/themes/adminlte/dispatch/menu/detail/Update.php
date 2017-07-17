<?php
namespace wocenter\backend\themes\adminlte\dispatch\menu\detail;

use wocenter\backend\modules\menu\models\Menu;
use wocenter\backend\modules\menu\models\MenuCategory;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatch\menu\detail
 */
class Update extends Dispatch
{

    use LoadModelTrait;

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run()
    {
        /** @var Menu $model */
        $model = $this->loadModel(Menu::className(), $this->_params['id']);
        $request = Yii::$app->getRequest();

        // 所选分类是否存在
        $category_name = MenuCategory::find()->select('name')->where(['id' => $model->category_id])->scalar();
        if (empty($category_name)) {
            $this->error('所选分类不存在');
        }

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, [
                    "/{$this->controller->getUniqueId()}",
                    'category' => $model->category_id,
                    $model->breadcrumbParentParam => $model->parent_id,
                ]);
            } else {
                $this->error($model->message);
            }
        }

        $breadcrumbs = $model->getBreadcrumbs(
            $model->id,
            $category_name,
            '/menu/detail',
            ['category' => $model->category_id],
            [
                -1 => ['label' => '菜单管理', 'url' => ['/menu']],
            ]
        );
        $menuList = Wc::$service->getMenu()->getMenus($model->category_id);

        return $this->assign([
            'model' => $model,
            'menuList' => $model->getTreeSelectList($menuList),
            'breadcrumbs' => $breadcrumbs,
            'title' => $breadcrumbs[$model->id]['label'],
        ])->display();
    }

}