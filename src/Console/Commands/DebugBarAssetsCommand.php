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
 * @since 2.0.0
 */

namespace Quantum\Console\Commands;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Console\QtCommand;

/**
 * Class DebugBarAssetsCommand
 * @package Quantum\Console\Commands
 */
class DebugBarAssetsCommand extends QtCommand
{

    /**
     * Command name
     * @var string
     */
    protected $name = 'core:debugbar';

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
     * @return mixed|void
     */
    public function exec()
    {
        $this->recursive_copy($this->vendorDebugbarFolderPath, $this->publicDebugbarFolderPath);

        $this->info('Debugbar assets successfully published');
    }

    /**
     * Recursively copies the debug bar assets
     * @param string $src
     * @param string $dst
     * @throws \RuntimeException
     */
    private function recursive_copy($src, $dst)
    {
        $dir = opendir($src);

        if ($dst != $this->publicDebugbarFolderPath) {
            if (@mkdir($dst, 777, true) === false) {
                throw new \RuntimeException(_message(ExceptionMessages::DIRECTORY_CANT_BE_CREATED, $dst));
            }
        }

        if (is_resource($dir)) {
            while (($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        $this->recursive_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        if ($file) {
                            @copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
            }

            closedir($dir);
        }
    }

}
