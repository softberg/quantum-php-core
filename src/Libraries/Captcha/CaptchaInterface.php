<?php
namespace Quantum\Libraries\Captcha;

interface CaptchaInterface
{
    public static function getInstance(array $params);

    public function display(string $formIdentifier, array $attributes = []);

    public function renderJs($lang = null, $callback = false, $onLoadClass = 'onloadCallBack');

    public function verifyResponse(string $response, string $clientIp = null);
}