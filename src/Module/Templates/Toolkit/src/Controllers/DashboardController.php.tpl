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
 * @since 2.9.8
 */

namespace Modules\Toolkit\Controllers;

use Quantum\Service\Exceptions\ServiceException;
use Modules\Toolkit\Services\DashboardService;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

/**
 * Class DashboardController
 * @package Modules\Toolkit
 */
class DashboardController extends BaseController
{

    /**
     * Email service
     * @var DashboardService
     */
    public $dashboardService;

    /**
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public function __before()
    {
        $this->dashboardService = ServiceFactory::get(DashboardService::class);

        parent::__before();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function index(Request $request, Response $response)
    {
        $this->view->setParams([
            'title' => 'Dashboard',
        ]);

        $response->html($this->view->render('pages/dashboard/index'));
    }
}