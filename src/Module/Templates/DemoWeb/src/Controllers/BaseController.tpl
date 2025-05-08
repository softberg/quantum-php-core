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
 * @since 2.9.7
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\View\Factories\ViewFactory;
use Quantum\Router\RouteController;
use Quantum\Libraries\Asset\Asset;
use Quantum\View\QtView;

/**
 * Class BaseController
 * @package Modules\Web
 */
abstract class BaseController extends RouteController
{

    /**
    * @var QtView
    */
    protected $view;

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before()
    {
        $this->view = ViewFactory::get();

        $this->view->setLayout(static::LAYOUT, [
            new Asset(Asset::CSS, 'shared/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, 'shared/css/easymde.min.css'),
            new Asset(Asset::CSS, '{{MODULE_NAME}}/css/custom.css'),
            new Asset(Asset::JS, 'shared/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'shared/js/materialize.min.js'),
            new Asset(Asset::JS, 'shared/js/easymde.min.js'),
            new Asset(Asset::JS, '{{MODULE_NAME}}/js/custom.js')
        ]);
    }
}