<?php

namespace Modules\Toolkit\Controllers;

use Quantum\View\Factories\ViewFactory;
use Quantum\Router\RouteController;
use Quantum\Libraries\Asset\Asset;

class MainController extends RouteController
{
    /**
     * Main layout
     */
    const LAYOUT = 'layouts/main';

    /**
     * Emails per page
     */
    const EMAILS_PER_PAGE = 10;

    /**
     * Logs per page
     */
    const LOGS_PER_PAGE = 10;

    /**
     * Rows per page
     */
    const ROWS_PER_PAGE = 20;

    /**
     * Current page
     */
    const CURRENT_PAGE = 1;

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