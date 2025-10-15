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

use Quantum\Http\Response;

/**
 * Class PageController
 * @package Modules\{{MODULE_NAME}}
 */
class PageController extends BaseController
{

    /**
     * Main layout
     */
    const LAYOUT = 'layouts/main';

    /**
     * Action - display home page  
     * @param Response $response
     */
    public function home(Response $response)
    {
        $this->view->setParams([
            'title' => config()->get('app_name'),
        ]);

        $response->html($this->view->render('pages/index'));
    }

    /**
     * Action - display about page 
     * @param Response $response
     */
    public function about(Response $response)
    {
        $this->view->setParams([
            'title' => t('common.about') . ' | ' . config()->get('app_name'),
        ]);

        $response->html($this->view->render('pages/about'));
    }
}