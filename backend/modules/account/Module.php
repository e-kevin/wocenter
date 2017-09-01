<?php
namespace wocenter\backend\modules\account;

use wocenter\backend\core\Modularity;

class Module extends Modularity
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\account\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'user';

}
