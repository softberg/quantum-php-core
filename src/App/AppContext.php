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

namespace Quantum\App;

use Quantum\App\Enums\AppType;
use InvalidArgumentException;

/**
 * Class AppContext
 * @package Quantum\App
 */
class AppContext
{
    private string $mode;

    public function __construct(string $mode)
    {
        if (!in_array($mode, [AppType::WEB, AppType::CONSOLE], true)) {
            throw new InvalidArgumentException("Invalid app mode: $mode");
        }

        $this->mode = $mode;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function isWebMode(): bool
    {
        return $this->mode === AppType::WEB;
    }

    public function isConsoleMode(): bool
    {
        return $this->mode === AppType::CONSOLE;
    }
}
