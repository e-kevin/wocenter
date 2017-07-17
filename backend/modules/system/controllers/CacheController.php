<?php
namespace wocenter\backend\modules\system\controllers;

use wocenter\backend\core\Controller;

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
