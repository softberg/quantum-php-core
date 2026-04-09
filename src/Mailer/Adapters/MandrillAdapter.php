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

namespace Quantum\Mailer\Adapters;

use Quantum\Mailer\Contracts\MailerInterface;
use Quantum\Mailer\Traits\MailerTrait;
use Quantum\HttpClient\HttpClient;
use Exception;

/**
 * class MandrillAdapter
 * @package Quantum\Mailer
 */
class MandrillAdapter implements MailerInterface
{
    use MailerTrait;

    public string $name = 'Mandrill';

    private string $apiUrl = 'https://mandrillapp.com/api/1.0/messages/send.json';

    private HttpClient $httpClient;

    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * MandrillAdapter constructor
     * @param array<string, mixed> $params
     */
    public function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->data['key'] = $params['api_key'];
    }

    /**
     * Prepares the data
     */
    protected function prepare(): void
    {
        $message = [];

        $message['from_email'] = $this->from['email'];

        if ($this->from['name']) {
            $message['from_name'] = $this->from['name'];
        }

        $message['to'] = $this->addresses;

        if ($this->subject) {
            $message['subject'] = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode('', $this->message) : $this->message;
            }

            $message['html'] = trim(str_replace("\n", '', $body));
        }

        $this->data['message'] = $message;
    }

    protected function sendEmail(): bool
    {
        try {
            $this->httpClient
                ->createRequest($this->apiUrl)
                ->setMethod('POST')
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return array<string>
     */
    protected function getTransportErrors(): array
    {
        return $this->httpClient->getErrors();
    }
}
