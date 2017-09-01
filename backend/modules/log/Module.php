<?php
namespace wocenter\backend\modules\log;

use wocenter\backend\core\Modularity;

class Module extends Modularity
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\log\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'action';

}
