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
use Quantum\Service\Factories\ServiceFactory;
use Modules\Toolkit\Services\LogsService;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

/**
 * Class LogsController
 * @package Modules\Toolkit
 */
class LogsController extends BaseController
{
    /**
     * Logs service
     * @var LogsService
     */
    public $logsService;

    /**
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public function __before()
    {
        $this->logsService = ServiceFactory::get(LogsService::class);

        parent::__before();
    }

    /**
     * @param Response $response
     */
    public function list(Response $response){
        $filteredLogFiles = $this->logsService->getLogFiles();

        $this->view->setParams([
            'title' => 'Logs',
            'logFiles' => $filteredLogFiles,
        ]);

        $response->html($this->view->render('pages/logs/index'));
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function single(Request $request, Response $response){
        $logFile = $request->get('logFile');
        $perPage = $request->get('per_page', self::ITEMS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $parsedLogs = $this->logsService->getLogEntries($logFile, $perPage, $currentPage);

        $this->view->setLayout('layouts/iframe');

        $this->view->setParams([
            'title' => 'Logs',
            'logData' => $parsedLogs->data(),
            'pagination' => $parsedLogs,
        ]);

        $response->html($this->view->render('pages/logs/log'));
    }
}