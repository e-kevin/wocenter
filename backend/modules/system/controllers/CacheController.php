<?php
namespace wocenter\backend\modules\system\controllers;

use wocenter\backend\core\Controller;

/**
 * Class CacheController
 * @package wocenter\backend\modules\system\controllers
 */
class CacheController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'flushCache' => [
                'class' => 'wocenter\backend\actions\FlushCache',
            ],
        ];
    }

}
