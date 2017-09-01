<?php
namespace wocenter\backend\modules\data;

use wocenter\backend\core\Modularity;

class Module extends Modularity
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\data\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'area-region';

}