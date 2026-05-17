<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Modules\Toolkit\Controllers;

use Quantum\Service\Exceptions\ServiceException;
use Modules\Toolkit\Services\DashboardService;
use Quantum\App\Exceptions\BaseException;
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
    public DashboardService $dashboardService;

    public function __before()
    {
        $this->dashboardService = service(DashboardService::class);

        parent::__before();
    }

    public function index(Request $request): Response
    {
        $this->view->setParams([
            'title' => 'Dashboard',
        ]);

        return response()->html($this->view->render('pages/dashboard/index'));
    }
}
