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

/**
 * Class GenerateSwaggerUiDocsCommand
 * @package Quantum\Console\Commands
 */
class GenerateSwaggerUiDocsCommand extends QtCommand
{
    /**
     * Command name
     * @var string
     */
    protected $name = 'install:swagger';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generates files for swagger ui';

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
    protected $help = 'The command will publish swagger ui resources';

    /**
     * Path to public debug bar resources
     * @var string 
     */
    private $publicSwaggerFolderPath = 'public/assets/SwaggerUi';

    /**
     * Path to vendor debug bar resources
     * @var string 
     */
    private $vendorSwaggerFolderPath = 'vendor/swagger-api/swagger-ui/dist';

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
        $fs = new FileSystem();
        $module = $this->getArgument('module');
        $modulePath = modules_dir() . DS . $module;
        $file = $modulePath . DS . 'Config' . DS . 'routes.php';
        $openApiRoutes = "
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Http\Response;
use Quantum\Di\Di;

return function (\$route) {
    //\$route->group('openapi', function (\$route) {
        \$route->get('" . strtolower($module) . "/documentation', function (Response \$response) {
            \$response->html(partial('swagger/swagger'));
        });

        \$route->get('" . strtolower($module) . "/docs', function (Response \$response) {
            \$fs = Di::get(FileSystem::class);
            \$response->json((array) json_decode(\$fs->get(modules_dir() . DS . '" . $module . "' . DS . 'Resources' . DS . 'swagger' . DS . 'docs.json', true)));
        //});
    });";
        if (strpos($fs->get($file), "\$route->group('openapi', function (\$route) {") === false) {
            $fs->put($file, str_replace('return function ($route) {', $openApiRoutes, $fs->get($file)));
        }
        if (!is_dir($modulePath)) {
            $this->error('The module ' . $module . ' not found');
            return;
        }

        if (!installed($modulePath . DS . 'swagger' . DS . 'docs.json')) {
            $fp = fopen($modulePath . DS . 'Resources' . DS . "swagger" . DS . "docs.json", "wb");
            fclose($fp);
        }

        exec(base_dir() . DS . 'vendor/bin/openapi modules/' . $module . '/Controllers/ -o modules/' . $module . '/Resources/swagger/docs.json');


        if (installed(assets_dir() . DS . 'SwaggerUi' . DS . 'index.css')) {
            $this->error('The swagger ui already installed');
            return;
        }

        $dir = opendir($this->vendorSwaggerFolderPath);

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if ($file && ($file != '.') && ($file != '..') && !in_array($file, $this->excludeFileNames)) {
                    copy($this->vendorSwaggerFolderPath . '/' . $file, $this->publicSwaggerFolderPath . '/' . $file);
                }
            }

            closedir($dir);
        }

        $this->info('Swagger assets successfully published');
    }
}
