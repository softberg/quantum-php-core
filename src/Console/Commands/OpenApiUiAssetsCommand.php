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
use Quantum\Console\QtCommand;
use Quantum\Di\Di;

/**
 * Class OpenApiUiAssetsCommand
 * @package Quantum\Console\Commands
 */
class OpenApiUiAssetsCommand extends QtCommand
{
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
        $this->fs = Di::get(FileSystem::class);
        $module = $this->getArgument('module');
        $modulePath = modules_dir() . DS . $module;
        $file = $modulePath . DS . 'Config' . DS . 'routes.php';
        $openapiRoutes = "
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Http\Response;
use Quantum\Di\Di;

return function (\$route) {
    //\$route->group('openapi', function (\$route) {
        \$route->get('" . strtolower($module) . "/documentation', function (Response \$response) {
            \$response->html(partial('openapi/openapi'));
        });

        \$route->get('" . strtolower($module) . "/docs', function (Response \$response) {
            \$fs = Di::get(FileSystem::class);
            \$response->json((array) json_decode(\$fs->get(modules_dir() . DS . '" . $module . "' . DS . 'Resources' . DS . 'openapi' . DS . 'docs.json', true)));
        //});
    });";

        if (!$this->fs->isDirectory($modulePath)) {
            $this->error('The module ' . $module . ' not found');
            return;
        }

        if (!$this->fs->exists(assets_dir() . DS . 'OpenApiUi' . DS . 'index.css')) {
            $dir = opendir($this->vendorOpenApiFolderPath);

            if (is_resource($dir)) {
                while (($fileUi = readdir($dir))) {
                    if ($fileUi && ($fileUi != '.') && ($fileUi != '..') && !in_array($fileUi, $this->excludeFileNames)) {
                        copy($this->vendorOpenApiFolderPath . '/' . $fileUi, $this->publicOpenApiFolderPath . '/' . $fileUi);
                    }
                }

                closedir($dir);
            }
        }

        if (strpos($this->fs->get($file), "\$route->group('openapi', function (\$route) {") === false) {
            $this->fs->put($file, str_replace('return function ($route) {', $openapiRoutes, $this->fs->get($file)));
        }

        if ($this->fs->exists($modulePath . DS . 'openApi' . DS . 'docs.json')) {
            $fp = fopen($modulePath . DS . 'Resources' . DS . "openApi" . DS . "docs.json", "w");
            fclose($fp);
        }

        exec(base_dir() . DS . 'vendor/bin/openapi modules/' . $module . '/Controllers/ -o modules/' . $module . '/Resources/openApi/docs.json');


        $this->info('OpenApi assets successfully published');
    }
}
