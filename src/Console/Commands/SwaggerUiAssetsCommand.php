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
