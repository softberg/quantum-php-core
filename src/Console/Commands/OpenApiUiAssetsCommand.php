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
class OpenApiUiAssetsCommand extends QtCommand
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
     * @var \string[][]
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
        $file = $modulePath . DS . 'Config' . DS . 'routes.php';
        $openapiRoutes = 'return function ($route) {
    $route->group("openapi", function ($route) {
        $route->get("' . strtolower($module) . '/documentation", function (Quantum\Http\Response $response) {
            $response->html(partial("openapi/openapi"));
        });

        $route->get("' . strtolower($module) . '/docs", function (Quantum\Http\Response $response) {
            $fs = Quantum\Di\Di::get(Quantum\Libraries\Storage\FileSystem::class);
            $response->json((array) json_decode($fs->get(modules_dir() . "' . DS . $module . DS . 'Resources' . DS . 'openapi' . DS . 'docs.json", true)));
        });
    });';

        if (!$this->fs->isDirectory($modulePath)) {
            $this->error('The module ' . $module . ' not found');
            return;
        }

        if (!$this->fs->exists(assets_dir() . DS . 'OpenApiUi' . DS . 'index.css')) {
            $dir = opendir($this->vendorOpenApiFolderPath);

            if (is_resource($dir)) {
                while (($fileUi = readdir($dir))) {
                    if ($fileUi && ($fileUi != '.') && ($fileUi != '..') && !in_array($fileUi, $this->excludeFileNames)) {
                        copy($this->vendorOpenApiFolderPath . DS . $fileUi, $this->publicOpenApiFolderPath . DS . $fileUi);
                    }
                }

                closedir($dir);
            }
        }

        if (!route_group_exists('openapi')) {
            $this->fs->put($file, str_replace('return function ($route) {', $openapiRoutes, $this->fs->get($file)));
        }

        if (!$this->fs->exists($modulePath . DS . 'Resources' . DS . 'openApi' . DS . 'docs.json')) {
            $this->fs->put($modulePath . DS . 'Resources' . DS . 'openApi' . DS . 'docs.json', '');
        }

        exec(base_dir() . DS . 'vendor' . DS . 'bin' . DS . 'openapi modules' . DS . $module . DS . 'Controllers' . DS . ' -o modules' . DS . $module .  DS . 'Resources' . DS . 'openApi' . DS . 'docs.json');

        $this->info('OpenApi assets successfully published');
    }
}
