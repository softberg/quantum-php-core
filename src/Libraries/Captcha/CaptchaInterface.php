<?php
namespace Quantum\Libraries\Captcha;

interface CaptchaInterface
{
    public function display(string $formIdentifier, array $attributes = []);

    public function verifyResponse(string $response, string $clientIp = null);

    public function getErrorCodes(): array;

    public function renderJs();
}