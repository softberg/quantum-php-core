<?php

namespace Modules\Toolkit\Controllers;

use Quantum\Service\Exceptions\ServiceException;
use Quantum\Service\Factories\ServiceFactory;
use Modules\Toolkit\Services\LogsService;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Asset\Asset;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

class LogsController extends MainController
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
    public function index(Response $response){
        $filteredLogFiles = $this->logsService->getLogFiles();

        $this->view->setParams([
            'title' => 'Logs',
            'logFiles' => $filteredLogFiles,
        ]);

        $response->html($this->view->render('pages/logFiles'));
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function view(Request $request, Response $response){
        $logDate = $request->get('logDate');
        $perPage = $request->get('per_page', self::LOGS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $parsedLogs = $this->logsService->getFileLogs($logDate, $perPage, $currentPage);

        $this->view->setLayout('layouts/iframe', [
            new Asset(Asset::CSS, 'Toolkit/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, 'Toolkit/css/toolkit.css'),
            new Asset(Asset::JS, 'Toolkit/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/materialize.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/toolkit.js')
        ]);

        $this->view->setParams([
            'title' => 'Logs',
            'parsedLogs' => $parsedLogs->data(),
            'pagination' => $parsedLogs,
        ]);

        $response->html($this->view->render('pages/logs'));
    }
}