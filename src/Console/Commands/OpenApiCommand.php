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

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Exceptions\DiException;
use Quantum\Router\RouteCollection;
use Quantum\Module\ModuleLoader;
use Quantum\Router\RouteBuilder;
use Quantum\Console\QtCommand;
use Quantum\Di\Di;
use OpenApi\Generator;

/**
 * Class OpenApiUiAssetsCommand
 * @package Quantum\Console
 */
class OpenApiCommand extends QtCommand
{
    /**
     * File System
     * @var FileSystem
     */
    protected FileSystem $fs;

    public function __construct()
    {
        parent::__construct();

        $this->fs = FileSystemFactory::get();
    }

    /**
     * Command name
     * @var string
     */
    protected $name = 'install:openapi';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generates files for OpenApi UI';

    /**
     * Command arguments
     * @var string[][]
     */
    protected $args = [
        ['module', 'required', 'The module name'],
    ];

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will publish OpenApi UI resources';

    /**
     * Path to public debug bar resources
     * @var string
     */
    private string $publicOpenApiFolderPath = 'public/assets/OpenApiUi';

    /**
     * Path to vendor debug bar resources
     * @var string
     */
    private string $vendorOpenApiFolderPath = 'vendor/swagger-api/swagger-ui/dist';

    /**
     * Exclude File Names
     * @var array
     */
    private array $excludeFileNames = ['index.html', 'swagger-initializer.js', 'favicon-16x16.png', 'favicon-32x32.png'];

    /**
     * Executes the command and generate Open API specifications
     * @throws BaseException
     * @throws ModuleException
     * @throws RouteException
     * @throws DiException
     */
    public function exec()
    {
        $moduleLoader = ModuleLoader::getInstance();

        $builder = new RouteBuilder();

        $routeCollection = $builder->build(
            $moduleLoader->loadModulesRoutes(),
            $moduleLoader->getModuleConfigs()
        );

        Di::set(RouteCollection::class, $routeCollection);

        $module = $this->getArgument('module');

        $modulePath = modules_dir() . DS . $module;

        $routes = $modulePath . DS . 'routes' . DS . 'routes.php';

        if (!$this->fs->exists(assets_dir() . DS . 'OpenApiUi' . DS . 'index.css')) {
            $this->copyResources();
            $this->info('OpenApi resources successfully published');
        }

        if (!$this->fs->isDirectory($modulePath)) {
            $this->error('The module `' . ucfirst($module) . '` not found');
            return;
        }

        if (route_group_exists('openapi', $module) && $this->fs->exists($modulePath . DS . 'resources' . DS . 'openApi' . DS . 'spec.json')) {
            $this->error('The Open API specifications already installed for `' . ucfirst($module) . '` module');
            return;
        }

        if (!route_group_exists('openapi', $module)) {
            $this->fs->put($routes, str_replace('return function ($route) {', $this->openapiRoutes($module), $this->fs->get($routes)));
        }

        if (!$this->fs->isDirectory($modulePath . DS . 'resources' . DS . 'openapi')) {
            $this->fs->makeDirectory($modulePath . DS . 'resources' . DS . 'openapi');
        }

        $this->generateOpenapiSpecification($module);
    }

    /**
     * Copies OpenApi resources
     */
    private function copyResources()
    {
        $dir = opendir($this->vendorOpenApiFolderPath);

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if (($file !== '.') && ($file !== '..') && !in_array($file, $this->excludeFileNames)) {
                    copy($this->vendorOpenApiFolderPath . DS . $file, $this->publicOpenApiFolderPath . DS . $file);
                }
            }

            closedir($dir);
        }
    }

    /**
     * Generates file with OpenApi specifications
     * @param string $module
     */
    private function generateOpenapiSpecification(string $module)
    {
        $annotationPath = modules_dir() . DS . $module . DS . 'Controllers' . DS . 'OpenApi' . DS;

        $specPath = modules_dir() . DS . $module . DS . 'resources' . DS . 'openapi' . DS . 'spec.json';

        $openApi = Generator::scan([$annotationPath]);

        $this->fs->put($specPath, $openApi->toJson());

        $this->info('OpenAPI specification generated successfully.');
    }

    /**
     * Gets the OpenApi routes
     * @param string $module
     * @return string
     */
    private function openapiRoutes(string $module): string
    {
        return 'return function ($route) {
    $route->group("openapi", function ($route) {
        $route->get("docs", function (Quantum\Http\Response $response) {
            $response->html(partial("openApi/openApi"));
        });

        $route->get("spec", function (Quantum\Http\Response $response) {
            $fs = Quantum\Libraries\Storage\Factories\FileSystemFactory::get();
            $response->json($fs->getJson(modules_dir() . "' . DS . $module . DS . 'resources' . DS . 'openapi' . DS . 'spec.json"));
        });
    });' . PHP_EOL;
    }
}
