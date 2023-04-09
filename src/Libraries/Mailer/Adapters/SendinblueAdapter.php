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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Curl\HttpClient;
use Quantum\Libraries\Mailer\MailTrap;
use Quantum\Exceptions\DiException;
use ReflectionException;
use Exception;

/**
 * class SendinblueAdapter
 * @package Quantum\Libraries\Mailer
 */
class SendinblueAdapter implements MailerInterface
{

    use MailerAdapterTrait;

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
    private function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
    }

    /**
     * Get Instance
     * @param array $params
     * @return SendinblueAdapter|null
     */
    public static function getInstance(array $params): ?SendinblueAdapter
    {
        if (self::$instance === null) {
            self::$instance = new self($params);
        }

        return self::$instance;
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
                $body = is_array($this->message) ? implode($this->message) : $this->message;
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

    /**
     * @return bool
     * @throws Exception
     */
    private function saveEmail(): bool
    {
        return MailTrap::getInstance()->saveMessage($this->getMessageId(), $this->getMessageContent());
    }
}
