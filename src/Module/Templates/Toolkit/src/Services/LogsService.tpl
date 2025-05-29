<?php

namespace Modules\Toolkit\Services;

use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Paginator\Paginator;
use Quantum\Service\QtService;
use ReflectionException;

class LogsService extends QtService
{

    /**
     * @return array
     * @throws BaseException
     * @throws ReflectionException
     */
    public function getLogFiles(): array
    {
        $logFiles = fs()->listDirectory(logs_dir());

        $filteredLogFiles = [];

        foreach($logFiles as $logFile){
            if(str_contains($logFile, '.log')){
                $filteredLogFiles[] =  pathinfo($logFile, PATHINFO_BASENAME);
            }
        }

        return $filteredLogFiles;
    }

    /**
     * @param string $logDate
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws ReflectionException
     */
    public function getFileLogs(string $logDate, int $perPage, int $currentPage): Paginator
    {
        $logs = fs()->get(logs_dir() . DS . $logDate);

        $parsedLogs = [];

        $pattern = '/\[(.*?)] (.*?): (.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\z)/s';

        preg_match_all($pattern, $logs, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $parsedLogs[] = [
                'date' => $match[1],
                'level' => $match[2],
                'message' => $match[3]
            ];
        }

        return $this->paginate($parsedLogs, $perPage, $currentPage);
    }

    /**
     * @param array $data
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     */
    private function paginate(array $data, int $perPage, int $currentPage): Paginator
    {
        return PaginatorFactory::create(Paginator::ARRAY, ["items" => $data, "perPage" => $perPage,"page" => $currentPage]);
    }
}