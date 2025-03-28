<?php

namespace Modules\Toolkit\Controllers;

use Modules\Toolkit\Services\DatabaseService;
use Quantum\Factory\ServiceFactory;
use Quantum\Libraries\Asset\Asset;
use Quantum\Factory\ViewFactory;
use Quantum\Mvc\QtService;
use Quantum\Http\Response;
use Quantum\Http\Request;

class DatabaseController extends MainController
{
    /**
     * @var QtService
     */
    private $databaseService;

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before(ViewFactory $view)
    {
        $this->databaseService = ServiceFactory::get(DatabaseService::class);

        parent::__before($view);
    }

    /**
     * @param Response $response
     * @param ViewFactory $view
     */
    public function index(Response $response, ViewFactory $view){

        $tables = $this->databaseService->getTables();

        $view->setParams([
            'title' => 'Database',
            'tables' => $tables,
        ]);

        $response->html($view->render('pages/databaseTables'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param ViewFactory $view
     */
    public function view(Request $request, Response $response,  ViewFactory $view){

        $tableName = $request->get('table');
        $perPage = $request->get('per_page', self::ROWS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $tableColumns = [];

        $table = $this->databaseService->getTable($tableName, $perPage, $currentPage);

        if(!empty($table->data)){
            foreach($table->data[0] as $key => $value){
                if ($key == "id"){
                    array_unshift($tableColumns, $key);
                }else{
                    $tableColumns[] = $key;
                }
            }
        }

        $view->setLayout('layouts/iframe', [
            new Asset(Asset::CSS, 'Toolkit/css/materialize.min.css', null, -1, ['media="screen,projection"']),
            new Asset(Asset::CSS, 'Toolkit/css/toolkit.css'),
            new Asset(Asset::JS, 'Toolkit/js/jquery-3.7.1.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/materialize.min.js'),
            new Asset(Asset::JS, 'Toolkit/js/toolkit.js')
        ]);

        $view->setParams([
            'title' => 'Database',
            'tableName' => $tableName,
            'tableColumns' => $tableColumns,
            'tableData' => $table->data(),
            'pagination' => $table,
        ]);

        $response->html($view->render('pages/databaseTable'));
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