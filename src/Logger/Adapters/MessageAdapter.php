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

namespace Quantum\Logger\Adapters;

use Quantum\Logger\Contracts\ReportableInterface;
use Quantum\Debugger\Debugger;

/**
 * Class MessageAdapter
 * @package Quantum\Logger
 */
class MessageAdapter implements ReportableInterface
{
    public function report(string $level, string $message, ?array $context = []): void
    {
        $tab = $context['tab'] ?? Debugger::MESSAGES;

        $debugger = debugbar();

        if ($debugger->isEnabled()) {
            $debugger->addToStoreCell($tab, $level, $message);
        }
    }

}
