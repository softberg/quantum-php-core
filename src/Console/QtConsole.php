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

namespace Quantum\Console;

use Symfony\Component\Console\Application;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use Quantum\Libraries\Lang\Lang;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;

/**
 * Class QtConsole
 * @package Quantum\Console
 */
class QtConsole
{

    /**
     * Console application name
     * @var string
     */
    private $name = 'Qt Console Application';

    /**
     * Console application version
     * @var string
     */
    private $version = '2.x';

    /**
     * Console application instance
     * @var Application
     */
    private $application;

    /**
     * Initialize the console application
     * @param string $baseDir
     * @return int
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\EnvException
     * @throws \Quantum\Exceptions\LangException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function init(string $baseDir): int
    {
        App::loadCoreFunctions($baseDir . DS . 'vendor' . DS . 'quantum' . DS . 'framework' . DS . 'src' . DS . 'Helpers');

        App::setBaseDir($baseDir);

        Di::loadDefinitions();

        $loader = Di::get(Loader::class);

        $loader->loadDir(base_dir() . DS . 'helpers');
        $loader->loadDir(base_dir() . DS . 'libraries');

        $this->application = new Application($this->name, $this->version);

        $this->register();

        Environment::getInstance()->load(new Setup('config', 'env'));

        Config::getInstance()->load(new Setup('config', 'config'));

        if (config()->get('multilang')) {
            Lang::getInstance((int)config()->get(Lang::LANG_SEGMENT))->load();
        }

        return $this->application->run();
    }

    /**
     * Registers commands
     */
    private function register()
    {
        $coreClassNames = get_directory_classes(framework_dir() . DS . 'Console' . DS . 'Commands');

        foreach ($coreClassNames as $coreClassName) {
            $coreCommandClass = '\\Quantum\\Console\\Commands\\' . $coreClassName;

            $coreCommand = new $coreCommandClass();

            if ($coreCommand instanceof QtCommand) {
                $this->application->add($coreCommand);
            }
        }

        $classNames = get_directory_classes(base_dir() . DS . 'shared' . DS . 'Commands');
        foreach ($classNames as $className) {
            $commandClass = '\\Shared\\Commands\\' . $className;

            $command = new $commandClass();

            if ($command instanceof QtCommand) {
                $this->application->add($command);
            }
        }
    }

}
