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
 * @since 2.6.0
 *
 */

namespace Quantum\Libraries\Asset;

/**
 * Class AssetManager
 * @package Quantum\Libraries\Asset
 */
class Asset
{
    const CSS = 1;

    const JS = 2;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $position;

    public function __construct(int $type, string $path, int $position = -1)
    {
        $this->type = $type;
        $this->path = $path;
        $this->position = $position;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

}