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
use Quantum\Di\Exceptions\DiException;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class MessageAdapter
 * @package Quantum\Logger
 */
class MessageAdapter implements ReportableInterface
{
    /**
     * @param array<string, mixed>|null $context
     * @throws DebugBarException
     */

    /**
     * @throws DiException|ReflectionException
     */
    public function report(string $level, string $message, ?array $context = []): void
    {
        $tab = $context['tab'] ?? Debugger::MESSAGES;

        if (!Di::isRegistered(Debugger::class)) {
            Di::register(Debugger::class);
        }

        $debugger = Di::get(Debugger::class);

        if ($debugger->isEnabled()) {
            $debugger->addToStoreCell($tab, $level, $message);
        }
    }

}
