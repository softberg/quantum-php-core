<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Console\Commands;

use Quantum\Console\QtCommand;
use Povils\Figlet\Figlet;

/**
 * Class VersionCommand
 * @package Quantum\Console
 */
class VersionCommand extends QtCommand
{
    /**
     * Command name
     */
    protected ?string $name = 'core:version';

    /**
     * Command description
     */
    protected ?string $description = 'Core version';

    /**
     * Command help text
     */
    protected ?string $help = 'Printing the current version of the framework into the terminal';

    /**
     * Executes the command and prints greetings into the terminal
     */
    public function exec(): void
    {
        $version = config()->get('app.version', 'UNKNOWN');

        $figlet = new Figlet();

        $renderedFiglet = $figlet
            ->setFontDir(assets_dir() . DS . 'shared' . DS . 'fonts' . DS . 'figlet' . DS)
            ->setFont('slant')
            ->render('QUANTUM PHP ' . $version);

        $this->info($renderedFiglet);

        $this->info('- - - Q U A N T U M   P H P   F R A M E W O R K  ' . $version . '  I N S T A L L E D - - -');
    }
}
