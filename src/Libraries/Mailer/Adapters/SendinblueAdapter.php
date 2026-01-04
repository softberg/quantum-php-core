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

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Traits\MailerTrait;
use Quantum\Libraries\HttpClient\HttpClient;
use Exception;

/**
 * class SendinblueAdapter
 * @package Quantum\Libraries\Mailer
 */
class SendinblueAdapter implements MailerInterface
{

    use MailerTrait;

    /**
     * @var string
     */
    public $name = 'Sendinblue';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl = 'https://api.sendinblue.com/v3/smtp/email';

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var SendinblueAdapter|null
     */
    private static $instance = null;

    /**
     * SendinblueAdapter constructor
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
    private function prepare()
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

            $this->data['htmlContent'] = trim(str_replace("\n", "", $body));
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
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'api-key' => $this->apiKey
                ])
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}