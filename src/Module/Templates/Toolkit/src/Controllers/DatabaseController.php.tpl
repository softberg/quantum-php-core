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

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Modules\Toolkit\Services\DatabaseService;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class DatabaseController
 * @package Modules\Toolkit
 */
class DatabaseController extends BaseController
{
    /**
     * @var DatabaseService
     */
    private DatabaseService $databaseService;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->databaseService = service(DatabaseService::class);

        parent::__before();
    }

    /**
     * @param Response $response
     */
    public function list(Response $response)
    {
        $tables = $this->databaseService->getTables();

        $this->view->setParams([
            'title' => 'Database',
            'tables' => $tables,
        ]);

        $response->html($this->view->render('pages/database/index'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws DatabaseException
     */
    public function single(Request $request, Response $response)
    {
        $tableName = $request->get('table');
        $perPage = $request->get('per_page', self::ITEMS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $tableData = $this->databaseService->getTableData($tableName, $perPage, $currentPage);

        $this->view->setLayout('layouts/iframe');

        $this->view->setParams([
            'title' => 'Database',
            'tableName' => $tableName,
            'tableColumns' => $tableData['columns'],
            'tableData' => $tableData['data'],
            'pagination' => $tableData['pagination'],
        ]);

        $response->html($this->view->render('pages/database/table'));
    }

    /**
     * @param Request $request
     */
    public function create(Request $request)
    {
        $tableName = $request->get('table');

        $newData = json_decode(htmlspecialchars_decode($request->get('data')), true);

        $this->databaseService->createTableRow($tableName, $newData);

        redirect(get_referrer());
    }

    /**
     * @param Request $request
     */
    public function update(Request $request)
    {
        $tableName = $request->get('table');
        $id = $request->get('rowId');
        $updatedData = json_decode(htmlspecialchars_decode($request->get('data')), true);

        $this->databaseService->updateTable($tableName, (int)$id, $updatedData);

        redirect(base_url(true) . '/database/view?table=' . $tableName);
    }

    /**
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $tableName = $request->get('tableName');
        $id = $request->get('id');

        $this->databaseService->deleteTableRow($tableName, $id);

        redirect(base_url(true) . '/database/view?table=' . $tableName);
    }
}
