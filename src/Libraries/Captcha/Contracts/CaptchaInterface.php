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

namespace Quantum\Libraries\Captcha\Contracts;

/**
 * Interface CaptchaInterface
 * @package Quantum\Libraries\Captcha
 */
interface CaptchaInterface
{
    /**
     * Max time difference
     */
    const MAX_TIME_DIFF = 60;

    /**
     * Captcha visible
     */
    const CAPTCHA_VISIBLE = 'visible';

    /**
     * Captcha invisible
     */
    const CAPTCHA_INVISIBLE = 'invisible';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self;

    /**
     * @param string $formIdentifier
     * @return mixed
     */
    public function addToForm(string $formIdentifier);

    /**
     * @param string $response
     * @return mixed
     */
    public function verify(string $response);

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;
}