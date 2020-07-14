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
 * Class WelcomeCommand
 * @package Quantum\Console\Commands
 */
class WelcomeCommand extends QtCommand
{

    /**
     * Command name
     * @var string
     */
    protected $name = 'core:welcome';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Installation greetings';

    /**
     * Command help text
     * @var string
     */
    protected $help = 'Printing greetings into the terminal';

    /**
     * Executes the command and prints greetings into the terminal
     * @return void
     */
    public function exec()
    {
        $text = <<< HEREDOC
   ____  __  _____    _   __________  ____  ___   ____  __  ______     ___    ____ 
  / __ \/ / / /   |  / | / /_  __/ / / /  |/  /  / __ \/ / / / __ \   |__ \  / __ \
 / / / / / / / /| | /  |/ / / / / / / / /|_/ /  / /_/ / /_/ / /_/ /   __/ / / / / /
/ /_/ / /_/ / ___ |/ /|  / / / / /_/ / /  / /  / ____/ __  / ____/   / __/_/ /_/ / 
\___\_\____/_/  |_/_/ |_/ /_/  \____/_/  /_/  /_/   /_/ /_/_/       /____(_)____/
                
HEREDOC;

        $this->info($text);
        $this->info('- - - Q U A N T U M   P H P   F R A M E W O R K   2.0   I N S T A L L E D - - -');
    }

}
