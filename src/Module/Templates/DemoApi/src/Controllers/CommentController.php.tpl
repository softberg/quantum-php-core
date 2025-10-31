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
use Quantum\Http\Response;
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
    }

    /**
     * Action - create comment
     * @param Request $request
     * @param Response $response
     * @param string|null $lang
     * @param string $uuid
     */
    public function create(Request $request, Response $response, ?string $lang, string $uuid)
    {
        $data = [
            'post_uuid' => $uuid,
            'user_uuid' => auth()->user()->uuid,
            'content' => trim($request->get('content')),
        ];

        $comment = $this->commentService->addComment($data);

        $response->json([
            'status' => 'success',
            'message' => t('common.created_successfully'),
            'data' => $comment
        ]);
    }

    /**
     * Action - delete comment
     * @param Response $response
     * @param string|null $lang
     * @param string $uuid
     */
    public function delete(Response $response, ?string $lang, string $uuid)
    {
        $this->commentService->deleteComment($uuid);

        $response->json([
            'status' => 'success',
            'message' => t('common.deleted_successfully'),
        ]);
    }
}
