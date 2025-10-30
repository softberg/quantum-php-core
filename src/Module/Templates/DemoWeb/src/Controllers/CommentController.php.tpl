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
 * @since 2.9.9
 */

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\Service\Factories\ServiceFactory;
use {{MODULE_NAMESPACE}}\Services\CommentService;
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
    public $commentService;

    public function __before()
    {
        $this->commentService = ServiceFactory::create(CommentService::class);
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
        $data = [
            'post_uuid' => $uuid,
            'user_uuid' => auth()->user()->uuid,
            'content' => trim($request->get('content')),
        ];

        if (!$data['content']) {
            session()->setFlash('error', t('common.comment_required'));
            redirect(get_referrer());
        }

        $this->commentService->addComment($data);

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
