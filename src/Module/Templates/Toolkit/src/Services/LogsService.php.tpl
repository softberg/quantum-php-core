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

namespace Modules\Toolkit\Services;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Paginator\Paginator;
use Quantum\Service\QtService;
use ReflectionException;

/**
 * Class LogsService
 * @package Modules\Toolkit
 */
class LogsService extends QtService
{
    /**
     * Retrieves a list of available log file names from the logs directory.
     * @return array
     * @throws BaseException
     * @throws ReflectionException
     */
    public function getLogFiles(): array
    {
        $logFiles = fs()->listDirectory(logs_dir());

        $filteredLogFiles = [];

        foreach ($logFiles as $logFile) {
            if (str_contains($logFile, '.log')) {
                $filteredLogFiles[] = fs()->fileNameWithExtension($logFile);
            }
        }

        return $filteredLogFiles;
    }

    /**
     * Parses a specific log file and returns paginated log entries.
     * @param string $logFile
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     * @throws ReflectionException
     * @throws ConfigException
     * @throws DiException
     */
    public function getLogEntries(string $logFile, int $perPage, int $currentPage): Paginator
    {
        $logs = fs()->get(logs_dir() . DS . $logFile);

        $parsedLogEntries = [];

        $pattern = '/\[(.*?)] (.*?): (.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\z)/s';

        preg_match_all($pattern, $logs, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $parsedLogEntries[] = [
                'date' => $match[1],
                'level' => $match[2],
                'message' => $match[3]
            ];
        }

        $parsedLogEntries = array_reverse($parsedLogEntries);

        return $this->paginate($parsedLogEntries, $perPage, $currentPage);
    }


    /**
     * Paginates an array of data.
     * @param array $data
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    private function paginate(array $data, int $perPage, int $currentPage): Paginator
    {
        return PaginatorFactory::create(Paginator::ARRAY, [
            "items" => $data,
            "perPage" => $perPage,
            "page" => $currentPage
        ]);
    }
}
