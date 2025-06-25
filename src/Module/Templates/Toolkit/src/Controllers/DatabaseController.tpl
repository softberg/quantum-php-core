<?php

namespace Modules\Toolkit\Controllers;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Modules\Toolkit\Services\DatabaseService;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\View\Factories\ViewFactory;
use Quantum\Libraries\Asset\Asset;
use Quantum\Http\Response;
use Quantum\Http\Request;

class DatabaseController extends MainController
{
    /**
     * @var DatabaseService
     */
    private $databaseService;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->databaseService = ServiceFactory::get(DatabaseService::class);

        parent::__before();
    }

    /**
     * @param Response $response
     */
    public function index(Response $response){

        $tables = $this->databaseService->getTables();

        $this->view->setParams([
            'title' => 'Database',
            'tables' => $tables,
        ]);

        $response->html($this->view->render('pages/databaseTables'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws DatabaseException
     */
    public function view(Request $request, Response $response){

        $tableName = $request->get('table');
        $perPage = $request->get('per_page', self::ROWS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $tableColumns = [];

        $table = $this->databaseService->getTable($tableName, $perPage, $currentPage);

        if(!empty($table->data())){
            foreach($table->firstItem()->asArray() as $key => $value){
                if ($key == "id"){
                    array_unshift($tableColumns, $key);
                }else{
                    $tableColumns[] = $key;
                }
            }
        }

        $this->view->setLayout('layouts/iframe', [
            new Asset(Asset::CSS, 'Toolkit/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, 'Toolkit/css/toolkit.css'),
            new Asset(Asset::JS, 'Toolkit/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/materialize.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/toolkit.js')
        ]);

        $this->view->setParams([
            'title' => 'Database',
            'tableName' => $tableName,
            'tableColumns' => $tableColumns,
            'tableData' => $table->data(),
            'pagination' => $table,
        ]);

        $response->html($this->view->render('pages/databaseTable'));
    }

    /**
     * @param Request $request
     */
    public function create(Request $request){
        $tableName = $request->get('table');

        $newData = json_decode(htmlspecialchars_decode($request->get('data')), true);

        $this->databaseService->createTableRow($tableName, $newData);

        redirect(get_referrer());
    }

    /**
     * @param Request $request
     */
    public function update(Request $request){
        $tableName = $request->get('table');
        $id = $request->get('rowId');
        $updatedData = json_decode(htmlspecialchars_decode($request->get('data')), true);

        $this->databaseService->updateTable($tableName, (int)$id, $updatedData);

        redirect(base_url(true) . '/database/view?table=' . $tableName);
    }

    /**
     * @param Request $request
     */
    public function delete(Request $request){
        $tableName = $request->get('tableName');
        $id = $request->get('id');

        $this->databaseService->deleteTableRow($tableName, $id);

        redirect(base_url(true) . '/database/view?table=' . $tableName);
    }
}