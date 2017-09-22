<?php
namespace wocenter\backend\modules\passport;

use wocenter\core\Modularity;

class Module extends Modularity
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\passport\controllers';

    /**
     * @inheritdoc
     */
    public $layout = 'passport';

}
