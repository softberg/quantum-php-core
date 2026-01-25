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

namespace Modules\Toolkit\Controllers;

use Quantum\App\Exceptions\BaseException;
use Quantum\Service\Exceptions\ServiceException;
use Modules\Toolkit\Services\EmailService;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

/**
 * Class EmailsController
 * @package Modules\Toolkit
 */
class EmailsController extends BaseController
{
    /**
     * @var EmailService
     */
    public EmailService $emailService;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->emailService = service(EmailService::class);

        parent::__before();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function list(Request $request, Response $response)
    {
        $perPage = $request->get('per_page', self::ITEMS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $data = $this->emailService->getEmails($perPage, $currentPage);

        $this->view->setParams([
            'title' => 'Emails',
            'emails' => $data->data(),
            'pagination' => $data
        ]);

        $response->html($this->view->render('pages/email/index'));
    }

    /**
     * @param Response $response
     * @param string $emailId
     */
    public function single(Response $response, string $emailId)
    {
        $email = $this->emailService->getEmail($emailId);

        $response->html(quoted_printable_decode($email->getParsedBody()));
    }

    /**
     * @param string $emailId
     */
    public function delete(string $emailId)
    {
        $this->emailService->deleteEmail($emailId);

        redirect(base_url(true) . '/emails');
    }
}
