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
    protected $description = 'Publishing debugbar assets';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will published debugbar assets';

    /**
     * Executes the command and publishes the debugbar assets
     * @return mixed|void
     */
    public function exec()
    {
        $vendorDebugbarAssetsPath = 'vendor/maximebf/debugbar/src/DebugBar/Resources';
        $publicDebugbarPath = 'public/assets/DebugBar/Resources';
        $this->recursive_copy($vendorDebugbarAssetsPath, $publicDebugbarPath);

        $this->info('Debugbar assets successfully published');
    }

    /**
     * Recursively copies the debugbar assets
     * @param string $src
     * @param string $dst
     * @return void
     */
    private function recursive_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recursive_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    if ($file)
                        copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

}
