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
 * class SendinblueAdapter
 * @package Quantum\Mailer
 */
class SendinblueAdapter implements MailerInterface
{
    use MailerTrait;

    public string $name = 'Sendinblue';

    /**
     * @var string
     */
    private $apiKey;

    private string $apiUrl = 'https://api.sendinblue.com/v3/smtp/email';

    private array $data = [];

    /**
     * SendinblueAdapter constructor
     */
    public function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
    }

    /**
     * Prepares the data
     */
    private function prepare(): void
    {
        $this->data['sender'] = $this->from;
        $this->data['to'] = $this->addresses;

        if ($this->subject) {
            $this->data['subject'] = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode('', $this->message) : $this->message;
            }

            $this->data['htmlContent'] = trim(str_replace("\n", '', $body));
        }
    }

    private function sendEmail(): bool
    {
        try {
            $this->httpClient
                ->createRequest($this->apiUrl)
                ->setMethod('POST')
                ->setHeaders([
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'api-key' => $this->apiKey,
                ])
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
