<?php
namespace wocenter\backend\modules\modularity;

use wocenter\backend\core\Modularity;

class Module extends Modularity
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\modularity\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'manage';

}
