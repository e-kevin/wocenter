<?php
namespace wocenter\backend\modules\passport;

use wocenter\backend\core\Modularity;
use yii\base\BootstrapInterface;
use yii\web\Application;

class Module extends Modularity implements BootstrapInterface
{

    /**
     * @inheritdoc
     */
    public $layout = 'passport';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'common';

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $public = "{$this->id}/{$this->defaultRoute}";
            $security = "{$this->id}/security";
            $app->getUrlManager()->addRules([
                'login' => "{$public}/login",
                'logout' => "{$public}/logout",
                'logout-on-step' => "{$public}/logout-on-step",
                'signup' => "{$public}/signup",
                'step' => "{$public}/step",
                'invite-signup' => "{$public}/invite-signup",
                'find-password' => "{$security}/find-password",
                'find-password-successful' => "{$security}/find-password-successful",
                'reset-password' => "{$security}/reset-password",
                'activate-account' => "{$security}/activate-account",
                'activate-user' => "{$security}/activate-user",
                'change-password' => "{$security}/change-password",
                ], false);
        }
    }

}
