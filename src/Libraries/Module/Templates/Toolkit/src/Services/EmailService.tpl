<?php

namespace Modules\Toolkit\Services;

use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Storage\Exceptions\FileSystemException;
use Quantum\Libraries\Database\Contracts\PaginatorInterface;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Modules\Toolkit\Paginator\Paginator;
use Quantum\Libraries\Mailer\MailTrap;
use Quantum\Mvc\QtService;

class EmailService extends QtService
{
    /**
     * @param int $perPage
     * @param int $currentPage
     * @return PaginatorInterface
     * @throws BaseException
     */
    public function getEmails(int $perPage, int $currentPage): PaginatorInterface
    {
        $mailTrap = MailTrap::getInstance();
        $emailsDirectory = base_dir() . DS . 'shared' . DS . 'emails';

        $fs = FileSystemFactory::get();

        $emailFiles = $fs->listDirectory($emailsDirectory);

        $emails = [];

        foreach ($emailFiles as $emailFile) {
            try{
                $email = $mailTrap->parseMessage(pathinfo($emailFile, PATHINFO_FILENAME));

                $emails[] = [
                    'id' => $email->getParsedMessageId(),
                    'subject' => $email->getParsedSubject(),
                    'recipient' => $email->getParsedToAddresses()[0],
                    'timestamp' => $email->getParsedDate()
                ];

                usort($emails, function ($a, $b) {
                    return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
                });

            }catch (FileSystemException $e){
                if($e->getMessage() === "exception.file_not_found"){
                    continue;
                }
            }
        }

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

        $emailFile = $this->getEmailFile($emailId);

        return $mailTrap->parseMessage($emailFile);
    }

    /**
     * @param string $emailId
     * @return bool
     * @throws BaseException
     */
    public function deleteEmail(string $emailId): bool
    {
        $fs = FileSystemFactory::get();

        $emailFile = $this->getEmailFile($emailId);

        return $fs->remove(base_dir() . DS . 'shared' . DS . 'emails'. DS . $emailFile . '.eml');
    }

    /**
     * @param string $emailId
     * @return string
     */
    private function getEmailFile(string $emailId): string
    {
        $emailId = urldecode($emailId);
        preg_match('/<(.*?)@/', preg_quote($emailId), $emailFile);

        return $emailFile[1];
    }

    /**
     * @param array $data
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     */
    private function paginate(array $data, int $perPage, int $currentPage): Paginator
    {
        return new Paginator($data, count($data), $perPage, $currentPage);
    }
}