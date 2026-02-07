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

namespace Quantum\Console\Commands;

use Quantum\Module\Exceptions\ModuleException;
use Quantum\Router\Exceptions\RouteException;
use Symfony\Component\Console\Helper\Table;
use Quantum\Di\Exceptions\DiException;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteBuilder;
use Quantum\Module\ModuleLoader;
use Quantum\Console\QtCommand;
use Quantum\Router\Route;
use Quantum\Di\Di;

/**
 * Class RouteListCommand
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
     * @var array<int, array<int, string>>
     */
    protected $options = [
        ['module', 'm', 'optional', 'Filter by module name'],
    ];

    /**
     * Executes the command
     * @throws DiException
     */
    public function exec()
    {
        try {
            $moduleLoader = ModuleLoader::getInstance();

            $builder = new RouteBuilder();
            $routeCollection = $builder->build(
                $moduleLoader->loadModulesRoutes(),
                $moduleLoader->getModuleConfigs()
            );

            Di::set(RouteCollection::class, $routeCollection);

            $routes = $routeCollection->all();

            $module = $this->getOption('module');

            if ($module) {
                $routes = array_filter(
                    $routes,
                    static function (Route $route) use ($module): bool {
                        $routeModule = $route->getModule();
                        return $routeModule !== null && strtolower($routeModule) === strtolower($module);
                    }
                );

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
     * @param Route $route
     * @param int $maxContentLength
     * @return array
     */
    private function composeTableRow(Route $route, int $maxContentLength = 25): array
    {
        $controller = $route->getController();
        $actionName = $route->getAction();

        if ($controller !== null && $actionName !== null) {
            $action = $controller . '@' . $actionName;
        } else {
            $action = 'Closure';
        }

        if (mb_strlen($action) > $maxContentLength) {
            $action = mb_substr($action, 0, $maxContentLength) . '...';
        }

        $middlewares = $route->getMiddlewares();
        $middlewaresString = $middlewares !== []
            ? implode(',', $middlewares)
            : '-';

        return [
            $route->getModule() ?? '',
            implode('|', $route->getMethods()),
            $route->getPattern(),
            $action,
            $middlewaresString,
        ];
    }
}
