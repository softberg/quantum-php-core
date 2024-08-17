<?php

namespace Quantum\Libraries\Captcha;

interface CaptchaInterface
{
    const MAX_TIME_DIFF = 60;

    const CAPTCHA_VISIBLE = 'visible';

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