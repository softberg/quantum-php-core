<?php

namespace Quantum\Composer;

class ComposerScripts
{
    private static function debugbarAssets()
    {
        echo "\e[1;32;mCopying Debugbar assets\e[0m\n";

        $vendor_debugbar_assets_path = 'vendor/maximebf/debugbar/src/DebugBar/Resources';
        $public_debugbar_path = 'public/assets/DebugBar/Resources';

        self::recursive_copy($vendor_debugbar_assets_path, $public_debugbar_path);
    }

    private static function recursive_copy($src, $dst)
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

    private static function copyingEnv()
    {
        echo "\e[1;32;mCopying .env file\e[0m\n";
        if(file_exists('.env.example')) {
            copy('.env.example', '.env');
        } else {
            echo "\e[1;31;m.env.example file not found\e[0m";
        }
    }

    public static function run()
    {
        self::debugbarAssets();
        self::copyingEnv();
    }


}