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
 * @since 2.9.8
 */

namespace Modules\Toolkit\Services;

use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Mailer\MailTrap;
use Quantum\Paginator\Paginator;
use Quantum\Service\QtService;
use ReflectionException;

/**
 * Class EmailService
 * @package Modules\Toolkit
 */
class EmailService extends QtService
{

    /**
     * @var string
     */
    private $emailsDirectory;


    public function __construct()
    {
        $this->emailsDirectory = base_dir() . DS . 'shared' . DS . 'emails';
    }

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws ReflectionException
     */
    public function getEmails(int $perPage, int $currentPage): Paginator
    {
        $mailTrap = MailTrap::getInstance();

        $emailFiles = fs()->listDirectory($this->emailsDirectory);

        $emails = [];

        foreach ($emailFiles as $emailFile) {
            try {
                $email = $mailTrap->parseMessage(fs()->fileName($emailFile));

                $emails[] = [
                    'id' => $this->getEmailId($email->getParsedMessageId()),
                    'subject' => $email->getParsedSubject(),
                    'recipient' => $email->getParsedToAddresses()[0] ?? null,
                    'timestamp' => $email->getParsedDate()
                ];


            } catch (FileSystemException $e) {
                continue;
            }
        }

        usort($emails, function ($a, $b) {
            return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
        });

        return $this->paginate($emails, $perPage, $currentPage);
    }

    /**
     * @param string $emailId
     * @return MailTrap
     * @throws BaseException
     */
    public function getEmail(string $emailId): MailTrap
    {
        $mailTrap = MailTrap::getInstance();

        return $mailTrap->parseMessage($emailId);
    }

    /**
     * @param string $emailId
     * @return bool
     * @throws BaseException
     * @throws ReflectionException
     */
    public function deleteEmail(string $emailId): bool
    {
        return fs()->remove($this->emailsDirectory . DS . $emailId . '.eml');
    }

    /**
     * @param string $messageId
     * @return string
     */
    private function getEmailId(string $messageId): string
    {
        preg_match('/<(.*?)@/', preg_quote($messageId), $matches);

        return $matches[1];
    }

    /**
     * @param array $data
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     * @throws BaseException
     * @throws PaginatorException
     */
    private function paginate(array $data, int $perPage, int $currentPage): Paginator
    {
        return PaginatorFactory::create(Paginator::ARRAY, [
            "items" => $data,
            "perPage" => $perPage,
            "page" => $currentPage
        ]);
    }
}