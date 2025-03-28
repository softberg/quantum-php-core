<?php

namespace Modules\Toolkit\Services;

use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Database\Contracts\PaginatorInterface;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\BaseException;
use Quantum\Mvc\QtService;

class DatabaseService extends QtService
{
    /**
     * @return array
     * @throws BaseException
     */
    public function getTables(): array
    {
        $storeDirectory = base_dir() . DS . 'shared' . DS . 'store';

        $fs = FileSystemFactory::get();

        $databaseFolder = $fs->listDirectory($storeDirectory);

        $tables = [];

        foreach ($databaseFolder as $item) {
            try{
                if($fs->isDirectory($item)){
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
     * @throws DatabaseException
     */
    public function getTable(string $tableName, int $perPage, int $currentPage): PaginatorInterface
    {
        $table = Database::getInstance()->getOrm($tableName);

        return $table->orderBy("id", "DESC")->paginate($perPage, $currentPage);
    }

    /**
     * Create table row
     * @param string $tableName
     * @param array $data
     * @return void
     * @throws DatabaseException
     */
    public function createTableRow(string $tableName, array $data): void
    {
        $table = Database::getInstance()->getOrm($tableName);

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
     * @throws DatabaseException
     */
    public function updateTable(string $tableName, int $id, array $data): void
    {
        $table = Database::getInstance()->getOrm($tableName);

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
     * @throws DatabaseException
     */
    public function deleteTableRow(string $tableName, int $id): void
    {
        $table = Database::getInstance()->getOrm($tableName);

        $table->findOne($id)->delete();
    }
}