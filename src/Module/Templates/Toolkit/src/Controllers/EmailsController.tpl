<?php

namespace Modules\Toolkit\Controllers;

use Quantum\Service\Exceptions\ServiceException;
use Quantum\Service\Factories\ServiceFactory;
use Modules\Toolkit\Services\EmailService;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;

class EmailsController extends MainController
{
    /**
     * Email service
     * @var EmailService
     */
    public $emailService;

    /**
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public function __before()
    {
        $this->emailService = ServiceFactory::get(EmailService::class);

        parent::__before();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function index(Request $request, Response $response)
    {
        $perPage = $request->get('per_page', self::EMAILS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $data = $this->emailService->getEmails($perPage, $currentPage);

        $this->view->setParams([
            'title' => 'Emails',
            'emails' => $data->data(),
            'pagination' => $data
        ]);

        $response->html($this->view->render('pages/emails'));
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function viewEmail(Request $request, Response $response)
    {
        $email = $this->emailService->getEmail($request->getQueryParam('emailId'));

        $response->html(quoted_printable_decode($email->getParsedBody()));
    }

    /**
     * @param Request $request
     */
    public function deleteEmail(Request $request)
    {
        $this->emailService->deleteEmail($request->getQueryParam('emailId'));

        redirect(base_url() . '/toolkit/emails');
    }
}