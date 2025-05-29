<?php

namespace Modules\Toolkit\Services;

use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Model\Factories\ModelFactory;
use Quantum\App\Exceptions\BaseException;
use Modules\Toolkit\Paginator\Paginator;
use Quantum\Service\QtService;
use ReflectionException;

class DatabaseService extends QtService
{
    /**
     * @return array
     * @throws BaseException
     * @throws ReflectionException
     */
    public function getTables(): array
    {
        $storeDirectory = base_dir() . DS . 'shared' . DS . 'store';

        $databaseFolder = fs()->listDirectory($storeDirectory);

        $tables = [];

        foreach ($databaseFolder as $item) {
            try{
                if(fs()->isDirectory($item)){
                    $tables[] = pathinfo($item, PATHINFO_FILENAME);
                }
            }catch (FileSystemException $e){
                if($e->getMessage() === "exception.file_not_found"){
                    continue;
                }
            }
        }

        return $tables;
    }

    /**
     * Get table data
     * @param string $tableName
     * @param int $perPage
     * @param int $currentPage
     * @return PaginatorInterface
     */
    public function getTable(string $tableName, int $perPage, int $currentPage): PaginatorInterface
    {
        $table = ModelFactory::createOrmInstance($tableName);

        $total = $table->count();

        $tableData = $table->orderBy("id", "DESC")->limit($perPage)->offset(($currentPage - 1) * $perPage)->get();

        return $this->paginate($tableData, $total, $perPage, $currentPage);
    }

    /**
     * Create table row
     * @param string $tableName
     * @param array $data
     * @return void
     */
    public function createTableRow(string $tableName, array $data): void
    {
        $table = ModelFactory::createOrmInstance($tableName);

        $row = $table->create();

        foreach($data as $field => $value){
            $row->prop($field, $value );
        }

        $row->save();
    }

    /**
     * @param string $tableName
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateTable(string $tableName, int $id, array $data): void
    {
        $table = ModelFactory::createOrmInstance($tableName);

        $row = $table->findOne($id);

        foreach($data as $field => $value){
            $row->prop($field, $value);
        }

        $row->save();
    }

    /**
     * @param string $tableName
     * @param int $id
     * @return void
     */
    public function deleteTableRow(string $tableName, int $id): void
    {
        $table = ModelFactory::createOrmInstance($tableName);

        $table->findOne($id)->delete();
    }

    /**
     * @param array $data
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @return PaginatorInterface
     */
    private function paginate(array $data, int $total, int $perPage, int $currentPage): PaginatorInterface
    {
        $params = [
            "items" => $data,
            "total" => $total,
            "perPage" => $perPage,
            "page" => $currentPage
        ];

        return Paginator::fromArray($params);
    }
}