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

namespace Modules\Toolkit\Services;

use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Paginator\Paginator;
use Quantum\Service\QtService;
use ReflectionException;

/**
 * Class DatabaseService
 * @package Modules\Toolkit
 */
class DatabaseService extends QtService
{

    /**
     * @var string
     */
    protected $storeDirectory;

    public function __construct()
    {
        $this->storeDirectory = base_dir() . DS . 'shared' . DS . 'store';
    }

    /**
     * Get tables
     * @return array
     * @throws BaseException
     * @throws ReflectionException
     */
    public function getTables(): array
    {
        $databaseFolder = fs()->listDirectory($this->storeDirectory);

        $tables = [];

        foreach ($databaseFolder as $item) {
            try {
                if (fs()->isDirectory($item)) {
                    $tables[] = fs()->fileName($item);
                }
            } catch (FileSystemException $e) {
                continue;
            }
        }

        return $tables;
    }

    /**
     * Check table existence
     * @param string $tableName
     * @param string|null $except
     * @return bool
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public function tableExists(string $tableName, ?string $except = null): bool
    {
        if ($except !== null && $tableName === $except) {
            return false;
        }

        return fs()->isDirectory($this->storeDirectory . DS . $tableName);
    }

    /**
     * Get table data
     * @param string $tableName
     * @param int $perPage
     * @param int $currentPage
     * @return array
     */
    public function getTableData(string $tableName, int $perPage, int $currentPage): array
    {
        $table = ModelFactory::createDynamicModel($tableName);

        $paginator = $table
            ->orderBy("id", "DESC")
            ->paginate($perPage, $currentPage);

        return [
            'columns' => $this->extractColumns($paginator),
            'data' => $paginator->data(),
            'pagination' => $paginator
        ];
    }

    /**
     * Create table row
     * @param string $tableName
     * @param array $data
     * @return void
     */
    public function createTableRow(string $tableName, array $data): void
    {
        $table = ModelFactory::createDynamicModel($tableName);

        $row = $table->create();

        foreach ($data as $field => $value) {
            $row->prop($field, $value);
        }

        $row->save();
    }

    /**
     * Update table row
     * @param string $tableName
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateTable(string $tableName, int $id, array $data): void
    {
        $table = ModelFactory::createDynamicModel($tableName);

        $row = $table->findOne($id);

        foreach ($data as $field => $value) {
            $row->prop($field, $value);
        }

        $row->save();
    }

    /**
     * Delete table row
     * @param string $tableName
     * @param int $id
     * @return void
     */
    public function deleteTableRow(string $tableName, int $id): void
    {
        $table = ModelFactory::createDynamicModel($tableName);

        $table->findOne($id)->delete();
    }

    /**
     * Extract columns
     * @param Paginator $paginator
     * @return array
     */
    private function extractColumns(Paginator $paginator): array
    {
        $firstRow = $paginator->firstItem();
        if (!$firstRow) {
            return [];
        }

        $columns = array_keys($firstRow->asArray());

        usort($columns, function ($a, $b) {
            if ($a === 'id') {
                return -1;
            }
            if ($b === 'id') {
                return 1;
            }
            return 0;
        });

        return $columns;
    }
}