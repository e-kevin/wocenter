<?php
namespace wocenter\backend\themes\adminlte\dispatch\system\setting;

use wocenter\backend\modules\system\models\ConfigForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use Yii;

/**
 * Class Update
 *
 * @package wocenter\backend\themes\adminlte\dispatch\system\setting
 */
class Update extends Dispatch
{

    use LoadModelTrait;

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $models = new ConfigForm(['categoryGroup' => $this->_params['id']]);
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            if ($models->load($request->getBodyParams()) && $models->save()) {
                $this->success(Yii::t('wocenter/app', 'Saved successful.'), ["{$this->controller->action->id}"]);
            } else {
                $this->error($models->message ?: Yii::t('wocenter/app', 'Saved failure.'));
            }
        }

        return $this->assign([
            'models' => $models,
            'id' => $this->_params['id'],
        ])->display();
    }

}