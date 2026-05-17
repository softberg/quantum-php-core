<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use {{MODULE_NAMESPACE}}\Services\CommandService;
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
    protected const LAYOUT = 'layouts/main';

    /**
     * Action - display home page
     */
    public function home(): Response
    {
        $this->view->setParams([
            'title' => config()->get('app.name'),
        ]);

        return response()->html($this->view->render('pages/index'));
    }

    /**
     * Action - display about page
     */
    public function about(): Response
    {
        $this->view->setParams([
            'title' => t('common.about') . ' | ' . config()->get('app.name'),
        ]);

        $commands = service(CommandService::class)->getAllCommands();

        return response()->html($this->view->render('pages/about', ['commands' => $commands]));
    }
}
