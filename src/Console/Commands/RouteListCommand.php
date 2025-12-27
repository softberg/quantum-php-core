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
 * @since 2.9.9
 */

namespace Quantum\Console\Commands;

use Quantum\Module\Exceptions\ModuleException;
use Quantum\Router\Exceptions\RouteException;
use Symfony\Component\Console\Helper\Table;
use Quantum\Module\ModuleLoader;
use Quantum\Console\QtCommand;
use Quantum\Router\Router;

/**
 * Class ServeCommand
 * @package Quantum\Console
 */
class RouteListCommand extends QtCommand
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'route:list';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Display all registered routes';

    /**
     * Command options
     * @var string
     */
    protected $options = [
        ['module', 'm', 'optional', 'Filter by module name'],
    ];

    /**
     * Executes the command
     */
    public function exec()
    {
        try {
            $modulesRoutes = ModuleLoader::getInstance()->loadModulesRoutes();

            Router::setRoutes($modulesRoutes);

            $routes = Router::getRoutes();

            $module = $this->getOption('module');

            if ($module) {
                $routes = array_filter($routes, fn($route) => strtolower($route['module']) === strtolower($module));

                if ($routes === []) {
                    $this->error('The module is not found');
                    return;
                }
            }

            $rows = [];

            foreach ($routes as $route) {
                $rows[] = $this->composeTableRow($route, 50);
            }

            $table = new Table($this->output);

            $table->setHeaderTitle('Routes')
                ->setHeaders(['MODULE', 'METHOD', 'URI', 'ACTION', 'MIDDLEWARE'])
                ->setRows($rows)
                ->render();
        } catch (ModuleException|RouteException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Composes a table row
     * @param array $route
     * @param int $maxContentLength
     * @return array
     */
    private function composeTableRow(array $route, int $maxContentLength = 25): array
    {
        $action = $route['action']
            . '\\'
            . $route['controller']
            . '@'
            . $route['action'];

        if (mb_strlen($action) > $maxContentLength) {
            $action = mb_substr($action, 0, $maxContentLength) . '...';
        }

        $middlewares = isset($route['middlewares'])
            ? implode(',', $route['middlewares'])
            : '-';

        return [
            $route['module'] ?? '',
            $route['method'] ?? '',
            $route['route'] ?? '',
            $action,
            $middlewares,
        ];
    }
}