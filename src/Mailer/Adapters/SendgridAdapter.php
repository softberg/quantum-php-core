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
 */

namespace Quantum\Mailer\Adapters;

use Quantum\Mailer\Contracts\MailerInterface;
use Quantum\Mailer\Traits\MailerTrait;
use Quantum\HttpClient\HttpClient;
use Exception;

/**
 * class SendgridAdapter
 * @package Quantum\Mailer
 */
class SendgridAdapter implements MailerInterface
{
    use MailerTrait;

    /**
     * @var string
     */
    public string $name = 'Sendgrid';

    /**
     * @var string
     */
    private $apiKey;

    private string $apiUrl = 'https://api.sendgrid.com/v3/mail/send';

    /**
     * @var array
     */
    private array $data = [];

    /**
     * SendgridAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
    }

    /**
     * Prepares the data
     */
    public function prepare(): void
    {
        $this->data['from'] = $this->from;

        $this->data['personalizations'] = [
            ['to' => $this->addresses],
        ];

        if ($this->subject) {
            $this->data['subject'] = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode('', $this->message) : $this->message;
            }

            $this->data['content'] = [
                [
                    'type' => 'text/html',
                    'value' => trim(str_replace("\n", '', $body)),
                ],
            ];
        }
    }

    /**
     * @return bool
     */
    private function sendEmail(): bool
    {
        try {
            $this->httpClient
                ->createRequest($this->apiUrl)
                ->setMethod('POST')
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
