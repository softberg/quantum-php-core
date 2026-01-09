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
 * @since 3.0.0
 *
 */

namespace Quantum\Libraries\Asset;

/**
 * Class Asset
 * @package Quantum\Libraries\Asset
 */
class Asset
{
    /**
     * Type CSS
     */
    public const CSS = 1;

    /**
     * Type JS
     */
    public const JS = 2;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var array|null
     */
    private $attributes = [];

    /**
     * @var int
     */
    private $position;

    /**
     * Asset templates
     * @var string[]
     */
    private $templates = [
        self::CSS => '<link rel="stylesheet" type="text/css" href="{%1}">',
        self::JS => '<script src="{%1}" {%2}></script>',
    ];

    /**
     * Asset constructor
     * @param int $type
     * @param string $path
     * @param string|null $name
     * @param int|null $position
     * @param array|null $attributes
     */
    public function __construct(int $type, string $path, ?string $name = null, ?int $position = -1, ?array $attributes = [])
    {
        $this->type = $type;
        $this->path = $path;
        $this->name = $name;
        $this->position = $position;
        $this->attributes = $attributes;
    }

    /**
     * Gets asset type
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Gets asset path
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets asset name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Gets asset position
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Gets asset attributes
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Gets asset url
     * @return string
     */
    public function url(): string
    {
        if (!parse_url($this->path, PHP_URL_HOST)) {
            return base_url() . '/assets/' . $this->path;
        }

        return $this->path;
    }

    /**
     * Renders asset tag
     * @return string
     */
    public function tag(): string
    {
        return _message(
            $this->templates[$this->type],
            [$this->url(), implode(' ', $this->attributes)]
        ) . PHP_EOL;
    }
}
