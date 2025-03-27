<?php

namespace Modules\Toolkit\Controllers;

use Modules\Toolkit\Services\LogsService;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\ServiceException;
use Quantum\Factory\ServiceFactory;
use Quantum\Libraries\Asset\Asset;
use Quantum\Factory\ViewFactory;
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
     * @param ViewFactory $view
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public function __before(ViewFactory $view)
    {
        $this->logsService = ServiceFactory::get(LogsService::class);

        parent::__before($view);
    }

    /**
     * @param Response $response
     * @param ViewFactory $view
     */
    public function index(Response $response, ViewFactory $view){
        $filteredLogFiles = $this->logsService->getLogFiles();

        $view->setParams([
            'title' => 'Logs',
            'logFiles' => $filteredLogFiles,
        ]);

        $response->html($view->render('pages/logFiles'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param ViewFactory $view
     */
    public function view(Request $request, Response $response, ViewFactory $view){
        $logDate = $request->get('logDate');
        $perPage = $request->get('per_page', self::LOGS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $parsedLogs = $this->logsService->getFileLogs($logDate, $perPage, $currentPage);

        $view->setLayout('layouts/iframe', [
            new Asset(Asset::CSS, 'Toolkit/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, 'Toolkit/css/toolkit.css'),
            new Asset(Asset::JS, 'Toolkit/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/materialize.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/toolkit.js')
        ]);

        $view->setParams([
            'title' => 'Logs',
            'parsedLogs' => $parsedLogs->data(),
            'pagination' => $parsedLogs,
        ]);

        $response->html($view->render('pages/logs'));
    }
}