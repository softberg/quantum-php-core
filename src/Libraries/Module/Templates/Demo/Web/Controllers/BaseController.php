<?php

use Quantum\Libraries\Module\ModuleManager;

$moduleManager = ModuleManager::getInstance();

return '<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.5
 */

namespace ' . $moduleManager->getBaseNamespace() . '\\' . $moduleManager->getModuleName() . '\Controllers;

use Quantum\Libraries\Asset\Asset;
use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtController;

/**
 * Class BaseController
 * @package Modules\Web
 */
abstract class BaseController extends QtController
{

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before(ViewFactory $view)
    {
        $view->setLayout(static::LAYOUT, [
            new Asset(Asset::CSS, \'css/materialize.min.css\', null, -1, [\'media="screen,projection"\']),
            new Asset(Asset::CSS, \'css/custom.css\'),
            new Asset(Asset::JS, \'js/jquery-3.7.1.min.js\'),
            new Asset(Asset::JS, \'js/materialize.min.js\'),
            new Asset(Asset::JS, \'js/custom.js\')
        ]);
    }
}';