<?php
namespace wocenter\backend\modules\gii;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{

    /**
     * @inheritdoc
     */
    public $name = '代码生成器';

    /**
     * @inheritdoc
     */
    public $description = '提供 WoCenter 系统开发所需的代码生成器';

    /**
     * @inheritdoc
     */
    public $bootstrap = true;

}
