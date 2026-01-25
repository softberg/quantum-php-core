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
 * @since 3.0.0
 */

namespace Modules\Toolkit\Controllers;

use Quantum\View\Factories\ViewFactory;
use Quantum\Router\RouteController;
use Quantum\Libraries\Asset\Asset;
use Quantum\View\QtView;

/**
 * Class BaseController
 * @package Modules\Toolkit
 */
class BaseController extends RouteController
{
    /**
     * Main layout
     */
    protected const LAYOUT = 'layouts/main';

    /**
     * Items per page
     */
    protected const ITEMS_PER_PAGE = 20;

    /**
     * Current page
     */
    protected const CURRENT_PAGE = 1;

    /**
     * @var QtView
     */
    protected QtView $view;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->view = ViewFactory::get();

        $this->view->setLayout(static::LAYOUT, [
            new Asset(Asset::CSS, 'Toolkit/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, 'Toolkit/css/toolkit.css'),
            new Asset(Asset::JS, 'Toolkit/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/materialize.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/toolkit.js')
        ]);
    }
}
