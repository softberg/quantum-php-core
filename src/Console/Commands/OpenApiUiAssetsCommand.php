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
 * Class OpenApiUiAssetsCommand
 * @package Quantum\Console\Commands
 */
class OpenApiUiAssetsCommand extends QtCommand
{
    /**
     * Command name
     * @var string
     */
    protected $name = 'install:openApi';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generates files for openApi ui';

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
    protected $help = 'The command will publish openApi ui resources';

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
            \$response->html(partial('openApi/openApi'));
        });

        \$route->get('" . strtolower($module) . "/docs', function (Response \$response) {
            \$fs = Di::get(FileSystem::class);
            \$response->json((array) json_decode(\$fs->get(modules_dir() . DS . '" . $module . "' . DS . 'Resources' . DS . 'openApi' . DS . 'docs.json', true)));
        //});
    });";
        if (strpos($fs->get($file), "\$route->group('openapi', function (\$route) {") === false) {
            $fs->put($file, str_replace('return function ($route) {', $openApiRoutes, $fs->get($file)));
        }
        if (!is_dir($modulePath)) {
            $this->error('The module ' . $module . ' not found');
            return;
        }

        if (!installed($modulePath . DS . 'opanApi' . DS . 'docs.json')) {
            $fp = fopen($modulePath . DS . 'Resources' . DS . "opanApi" . DS . "docs.json", "wb");
            fclose($fp);
        }

        exec(base_dir() . DS . 'vendor/bin/openapi modules/' . $module . '/Controllers/ -o modules/' . $module . '/Resources/opanApi/docs.json');


        if (installed(assets_dir() . DS . 'OpenApiUi' . DS . 'index.css')) {
            $this->error('The opanApi ui already installed');
            return;
        }

        $dir = opendir($this->vendorOpenApiFolderPath);

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if ($file && ($file != '.') && ($file != '..') && !in_array($file, $this->excludeFileNames)) {
                    copy($this->vendorOpenApiFolderPath . '/' . $file, $this->publicOpenApiFolderPath . '/' . $file);
                }
            }

            closedir($dir);
        }

        $this->info('OpenApi assets successfully published');
    }
}
