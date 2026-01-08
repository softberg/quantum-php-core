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

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Traits\MailerTrait;
use Quantum\Libraries\HttpClient\HttpClient;
use Exception;

/**
 * class MandrillAdapter
 * @package Quantum\Libraries\Mailer
 */
class MandrillAdapter implements MailerInterface
{

    use MailerTrait;

    /**
     * @var string
     */
    public $name = 'Mandrill';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    private $apiUrl = 'https://mandrillapp.com/api/1.0/messages/send.json';

    /**
     * @var array
     */
    private $data = [];

    /**
     * MandrillAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->data['key'] = $params['api_key'];
    }

    /**
     * Prepares the data
     */
    private function prepare()
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

            $message['html'] = trim(str_replace("\n", "", $body));
        }

        $this->data['message'] = $message;
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
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}