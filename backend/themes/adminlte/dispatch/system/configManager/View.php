<?php
namespace wocenter\backend\themes\adminlte\dispatch\system\configManager;

use wocenter\backend\modules\system\models\Config;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use wocenter\Wc;
use Yii;

/**
 * Class View
 *
 * @package wocenter\backend\themes\adminlte\dispatch\system\configManager
 */
class View extends Dispatch
{

    use LoadModelTrait;

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        return $this->assign([
            'model' => $this->loadModel(Config::className(), $this->_params['id']),
            'configGroupList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_GROUP_LIST'),
            'configTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
        ])->display();
    }

}