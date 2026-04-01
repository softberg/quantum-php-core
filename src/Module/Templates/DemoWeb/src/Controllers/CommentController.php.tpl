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

namespace {{MODULE_NAMESPACE}}\Controllers;

use {{MODULE_NAMESPACE}}\Services\CommentService;
use {{MODULE_NAMESPACE}}\DTOs\CommentDTO;
use Quantum\Http\Request;

/**
 * Class CommentController
 * @package Modules\{{MODULE_NAME}}
 */
class CommentController extends BaseController
{

    /**
     * @var CommentService
     */
    public CommentService $commentService;

    /**
     * @return void
     * @throws ReflectionException
     * @throws \Quantum\App\Exceptions\BaseException
     * @throws \Quantum\Di\Exceptions\DiException
     * @throws \Quantum\Service\Exceptions\ServiceException
     */
    public function __before()
    {
        $this->commentService = service(CommentService::class);
        parent::__before();
    }

    /**
     * Action - create comment
     * @param Request $request
     * @param string|null $lang
     * @param string $uuid
     */
    public function create(Request $request, ?string $lang, string $uuid)
    {
        $commentDto = CommentDTO::fromRequest($request, $uuid, auth()->user()->uuid);

        $this->commentService->addComment($commentDto);

        session()->setFlash('success', t('common.comment_added'));
        redirect(get_referrer());
    }

    /**
     * Action - delete comment
     * @param string|null $lang
     * @param string $uuid
     */
    public function delete(?string $lang, string $uuid)
    {
        $this->commentService->deleteComment($uuid);

        session()->setFlash('success', t('common.comment_deleted'));
        redirect(get_referrer());
    }
}
