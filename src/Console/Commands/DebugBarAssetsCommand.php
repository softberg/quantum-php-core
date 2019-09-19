<?php

namespace Quantum\Console\Commands;

use Quantum\Console\Qt_Command;

class DebugBarAssetsCommand extends Qt_Command
{
    protected $name = 'core:debugbar';

    protected $description = 'Published debugbar assets';

    protected $help = 'Published debugbar assets';


    public function exec()
    {
        $vendor_debugbar_assets_path = 'vendor/maximebf/debugbar/src/DebugBar/Resources';
        $public_debugbar_path = 'public/assets/DebugBar/Resources';
        $this->recursive_copy($vendor_debugbar_assets_path, $public_debugbar_path);

        $this->info('published debugbar assets');
    }

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