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

namespace Quantum\Libraries\Logger\Adapters;

use Quantum\Libraries\Logger\Contracts\ReportableInterface;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;

/**
 * Class MessageAdapter
 * @package Quantum\Logger
 */
class MessageAdapter implements ReportableInterface
{

    /**
     * @param string $level
     * @param $message
     * @param array|null $context
     * @return void
     * @throws DebugBarException
     */
    public function report(string $level, $message, ?array $context = [])
    {
        $tab = $context['tab'] ?? Debugger::MESSAGES;

        $debugger = Debugger::getInstance();

        if ($debugger->isEnabled()) {
            $debugger->addToStoreCell($tab, $level, $message);
        }
    }

}