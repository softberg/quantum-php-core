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
 * @since 2.8.0
 */

namespace Quantum\Console\Commands;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Router\ModuleLoader;
use Quantum\Console\QtCommand;
use Quantum\Di\Di;

/**
 * Class OpenApiUiAssetsCommand
 * @package Quantum\Console\Commands
 */
class OpenApiCommand extends QtCommand
{

    /**
     * File System
     * @var \Quantum\Libraries\Storage\FileSystem
     */
    protected $fs;

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
    private $publicOpenApiFolderPath = 'public/assets/OpenApiUi';

    /**
     * Path to vendor debug bar resources
     * @var string 
     */
    private $vendorOpenApiFolderPath = 'vendor/swagger-api/swagger-ui/dist';

    /**
     * Exclude File Names
     * @var array
     */
    private $excludeFileNames = ['index.html', 'swagger-initializer.js', 'favicon-16x16.png', 'favicon-32x32.png'];

    /**
     * Executes the command and publishes the debug bar assets
     */
    public function exec()
    {
        ModuleLoader::loadModulesRoutes();

        $this->fs = Di::get(FileSystem::class);

        $module = $this->getArgument('module');

        $modulePath = modules_dir() . DS . $module;

        $routes = $modulePath . DS . 'Config' . DS . 'routes.php';

        if (!$this->fs->exists(assets_dir() . DS . 'OpenApiUi' . DS . 'index.css')) {
            $this->copyResources();
        }

        if (!$this->fs->isDirectory($modulePath)) {
            $this->error('The module `' . ucfirst($module) . '` not found');
            return;
        }

        if (route_group_exists('openapi', $module) && $this->fs->exists($modulePath . DS . 'Resources' . DS . 'openApi' . DS . 'spec.json')) {
            $this->error('The Open API sepcifications already installed for `' . ucfirst($module) . '` module');
            return;
        }

        if (!route_group_exists('openapi', $module)) {
            $this->fs->put($routes, str_replace('return function ($route) {', $this->openapiRoutes($module), $this->fs->get($routes)));
        }

        if (!$this->fs->isDirectory($modulePath . DS . 'Resources' . DS . 'openapi')) {
            $this->fs->makeDirectory($modulePath . DS . 'Resources' . DS . 'openapi');
        }

        $this->generateOpenapiSpecification($module);

        $this->info('OpenApi recources successfully published');
    }

    /**
     * Copies OpenApi Resources
     */
    private function copyResources()
    {
        $dir = opendir($this->vendorOpenApiFolderPath);

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if (($file != '.') && ($file != '..') && !in_array($file, $this->excludeFileNames)) {
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
        exec(base_dir() . DS . 'vendor' . DS . 'bin' . DS . 'openapi ' . modules_dir() . DS . $module . DS . 'Controllers' . DS . ' -o ' . modules_dir() . DS . $module . DS . 'Resources' . DS . 'openapi' . DS . 'spec.json');
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
            $response->html(partial("openapi/openapi"));
        });

        $route->get("spec", function (Quantum\Http\Response $response) {
            $fs = Quantum\Di\Di::get(Quantum\Libraries\Storage\FileSystem::class);
            $response->json((array) json_decode($fs->get(modules_dir() . "' . DS . $module . DS . 'Resources' . DS . 'openapi' . DS . 'spec.json")));
        });
    });' . PHP_EOL;
    }

}
