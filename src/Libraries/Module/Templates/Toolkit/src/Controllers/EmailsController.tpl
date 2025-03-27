<?php

namespace Modules\Toolkit\Controllers;

use Modules\Toolkit\Services\EmailService;
use Quantum\Exceptions\ServiceException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Factory\ServiceFactory;
use Quantum\Factory\ViewFactory;
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
     * @param ViewFactory $view
     * @throws DiException
     * @throws ServiceException
     * @throws ReflectionException
     */
    public function __before(ViewFactory $view)
    {
        $this->emailService = ServiceFactory::get(EmailService::class);

        parent::__before($view);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param ViewFactory $view
     */
    public function index(Request $request, Response $response, ViewFactory $view)
    {
        $perPage = $request->get('per_page', self::EMAILS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);

        $data = $this->emailService->getEmails($perPage, $currentPage);

        $view->setParams([
            'title' => 'Emails',
            'emails' => $data->data(),
            'pagination' => $data
        ]);

        $response->html($view->render('pages/emails'));
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