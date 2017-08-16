<?php
namespace wocenter\backend\themes\adminlte\dispatch\account\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\helpers\ArrayHelper;
use wocenter\libs\Constants;
use wocenter\models\UserSearch;
use wocenter\Wc;
use Yii;
use yii\base\InvalidParamException;

/**
 * Class Search
 *
 * @package wocenter\backend\themes\adminlte\dispatch\account\user
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new UserSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => $this->getSearchAction(),
            'registerTypeList' => ArrayHelper::merge(
                [Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')],
                Wc::$service->getSystem()->getConfig()->extra('REGISTER_TYPE')
            ),
            'genderList' => ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getGenderList()),
        ])->display('_search');
    }

    /**
     * 获取搜索地址
     *
     * @return array
     */
    private function getSearchAction()
    {
        ($referer = Yii::$app->getRequest()->get('referer'));
        if (in_array($referer, ['account/user/index', 'account/user/forbidden-list', 'account/user/locked-list'])) {
            return Yii::$app->getUrlManager()->createUrl([$referer, 'from-box' => Yii::$app->getRequest()->get('from-box')]);
        } else {
            throw new InvalidParamException('缺少必要参数: ' . "{referer}");
        }
    }

}
