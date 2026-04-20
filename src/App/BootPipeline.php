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

use Quantum\App\Contracts\BootStageInterface;
use InvalidArgumentException;

/**
 * Class BootPipeline
 * @package Quantum\App
 */
class BootPipeline
{
    /**
     * @var BootStageInterface[]
     */
    private array $stages;

    /**
     * @param BootStageInterface[] $stages
     */
    public function __construct(array $stages = [])
    {
        foreach ($stages as $stage) {
            if (!$stage instanceof BootStageInterface) {
                throw new InvalidArgumentException(
                    'All stages must implement ' . BootStageInterface::class
                );
            }
        }

        $this->stages = $stages;
    }

    public function run(AppContext $context): void
    {
        foreach ($this->stages as $stage) {
            $stage->process($context);
        }
    }
}
