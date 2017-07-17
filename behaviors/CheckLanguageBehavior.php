<?php
namespace wocenter\behaviors;

use Yii;
use yii\base\Application;
use yii\base\Behavior;
use yii\web\Cookie;

/**
 * 语言检测行为类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class CheckLanguageBehavior extends Behavior
{

    public $languageParam = '_lang';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'checkLanguage'
        ];
    }

    public function checkLanguage()
    {
        $request = Yii::$app->getRequest();
        if ($request->get($this->languageParam)) {
            // By passing a parameter to change the language
            Yii::$app->language = htmlspecialchars($request->get($this->languageParam));

            // get the cookie collection (yii\web\CookieCollection) from the "response" component
            $cookies = Yii::$app->getResponse()->getCookies();
            // add a new cookie to the response to be sent
            $cookies->add(new Cookie([
                'name' => $this->languageParam,
                'value' => Yii::$app->language,
                'expire' => time() + (365 * 24 * 60 * 60),
            ]));
        } elseif (isset($request->cookies[$this->languageParam]) && $request->cookies[$this->languageParam]->value != "") {
            // COOKIE in accordance with the language type to set the language
            Yii::$app->language = $request->cookies[$this->languageParam]->value;
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // According to the browser language to set the language
            $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            Yii::$app->language = $lang[0];
        }
    }

}
