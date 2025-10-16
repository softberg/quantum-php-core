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
 * @since 2.9.9
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\View\Factories\ViewFactory;
use Quantum\Router\RouteController;
use Quantum\Libraries\Asset\Asset;
use Quantum\Http\Response;

/**
 * Class MainController
 * @package Modules\Web
 */
class MainController extends RouteController
{

    /**
     * Main layout
     */
    const LAYOUT = 'layouts/main';

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before(ViewFactory $view)
    {
        $view->setLayout(static::LAYOUT, [
            new Asset(Asset::CSS, 'shared/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, '{{MODULE_NAME}}/css/custom.css')
        ]);
    }
    
    /**
     * Action - display home page
     * @param Response $response
     * @param ViewFactory $view
     */
    public function index(Response $response, ViewFactory $view)
    {   
        $view->setParams([
            'title' => config()->get('app.name'),
        ]);
        
        $response->html($view->render('index'));
    }
}