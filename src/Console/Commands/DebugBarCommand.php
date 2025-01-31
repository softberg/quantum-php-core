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
 * @since 2.9.5
 */

namespace Quantum\Console\Commands;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\BaseException;
use Quantum\Console\QtCommand;

/**
 * Class DebugBarAssetsCommand
 * @package Quantum\Console
 */
class DebugBarCommand extends QtCommand
{
    /**
     * File System
     * @var FileSystem
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
    private $publicDebugBarFolderPath = 'public/assets/DebugBar/Resources';

    /**
     * Path to vendor debug bar resources
     * @var string
     */
    private $vendorDebugBarFolderPath = 'vendor/maximebf/debugbar/src/DebugBar/Resources';

    /**
     * Executes the command and publishes the debug bar assets
     * @throws LangException
     * @throws BaseException
     */
    public function exec()
    {
        $this->fs = FileSystemFactory::get();

        if ($this->fs->exists(assets_dir() . DS . 'DebugBar' . DS . 'Resources' . DS . 'debugbar.css')) {
            $this->error('The debug ber already installed');
            return;
        }

        $this->copyResources($this->vendorDebugBarFolderPath, $this->publicDebugBarFolderPath);

        $this->info('Debugbar resources successfully published');
    }

    /**
     * Recursively copies the debug bar assets
     * @param string $src
     * @param string $dst
     * @throws LangException
     */
    private function copyResources(string $src, string $dst)
    {
        $dir = opendir($src);

        if ($dst != $this->publicDebugBarFolderPath) {
            if ($this->fs->makeDirectory($dst) === false) {
                throw new \RuntimeException(t('exception.directory_cant_be_created', $dst));
            }
        }

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if ($this->fs->isDirectory($src . DS . $file)) {
                        $this->copyResources($src . DS . $file, $dst . DS . $file);
                    } else {
                        copy($src . DS . $file, $dst . DS . $file);
                    }
                }
            }

            closedir($dir);
        }
    }
}
