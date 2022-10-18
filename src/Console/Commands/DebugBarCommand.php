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
 * Class DebugBarAssetsCommand
 * @package Quantum\Console\Commands
 */
class DebugBarAssetsCommand extends QtCommand
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
    protected $name = 'install:debugbar';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Publishes debugbar assets';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will publish debugbar assets';

    /**
     * Path to public debug bar resources
     * @var string 
     */
    private $publicDebugbarFolderPath = 'public/assets/DebugBar/Resources';

    /**
     * Path to vendor debug bar resources
     * @var string 
     */
    private $vendorDebugbarFolderPath = 'vendor/maximebf/debugbar/src/DebugBar/Resources';

    /**
     * Executes the command and publishes the debug bar assets
     */
    public function exec()
    {
        $this->fs = Di::get(FileSystem::class);
        
        if ($this->fs->exists(assets_dir() . DS . 'DebugBar' . DS . 'Resources' . DS . 'debugbar.css')) {
            $this->error('The debuger already installed');
            return;
        }

        $this->copyResources($this->vendorDebugbarFolderPath, $this->publicDebugbarFolderPath);

        $this->info('Debugbar resources successfully published');
    }

    /**
     * Recursively copies the debug bar assets
     * @param string $src
     * @param string $dst
     * @throws \RuntimeException
     */
    private function copyResources(string $src, string $dst)
    {
        $dir = opendir($src);

        if ($dst != $this->publicDebugbarFolderPath) {
            if ($this->fs->makeDirectory($dst, 777, true) === false) {
                throw new \RuntimeException(t('exception.directory_cant_be_created', $dst));
            }
        }

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if ($this->fs->isDirectory($src . DS . $file)) {
                        $this->recursive_copy($src . DS . $file, $dst . DS . $file);
                    } else {
                        if ($file) {
                            copy($src . DS . $file, $dst . DS . $file);
                        }
                    }
                }
            }

            closedir($dir);
        }
    }
}
