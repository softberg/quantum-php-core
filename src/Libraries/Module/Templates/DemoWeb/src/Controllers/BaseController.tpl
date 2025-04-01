<?php

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

namespace {{MODULE_NAMESPACE}}\Controllers;

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
            new Asset(Asset::CSS, 'shared/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, '{{MODULE_NAME}}/css/custom.css'),
            new Asset(Asset::JS, 'shared/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'shared/js/materialize.min.js'),
            new Asset(Asset::JS, '{{MODULE_NAME}}/js/custom.js')
        ]);
    }
}