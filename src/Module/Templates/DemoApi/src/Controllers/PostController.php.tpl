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
use Quantum\Http\Constants\StatusCode;
use {{MODULE_NAMESPACE}}\Services\CommentService;
use {{MODULE_NAMESPACE}}\Services\PostService;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class PostController
 * @package Modules\{{MODULE_NAME}}
 */
class PostController extends BaseController
{
    /**
     * Posts per page
     */
    const POSTS_PER_PAGE = 8;

    /**
     * Current page
     */
    const CURRENT_PAGE = 1;

    /**
     * Post service
     * @var PostService
     */
    public $postService;

    /**
     * Works before an action
     */
    public function __before()
    {
        $this->postService = ServiceFactory::create(PostService::class);
    }

    /**
     * Action - get posts list
     * @param Request $request
     * @param Response $response
     */
    public function posts(Request $request, Response $response)
    {
        $perPage = $request->get('per_page', self::POSTS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);
        $search = trim($request->get('q'));

        $paginatedPosts = $this->postService->getPosts($perPage, $currentPage, $search);

        $response->json([
            'status' => 'success',
            'data' => $this->postService->transformData($paginatedPosts->data()->all()),
            'pagination' => [
                'total_records' => $paginatedPosts->total(),
                'current_page' => $paginatedPosts->currentPageNumber(),
                'next_page' => $paginatedPosts->nextPageNumber(),
                'prev_page' => $paginatedPosts->previousPageNumber(),
            ]
        ]);
    }

    /**
     * Action - get single post
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     */
    public function post(Response $response, ?string $lang, string $postUuid)
    {
        $post = $this->postService->getPost($postUuid);

        if ($post->isEmpty()) {
            $response->json([
                'status' => 'error',
                'message' => t('common.post_not_found')
            ], StatusCode::NOT_FOUND);

            stop();
        }

        $postData = current($this->postService->transformData([$post]));

        $commentService = ServiceFactory::create(CommentService::class);
        $comments = $commentService->getCommentsByPost($postUuid);

        $commentsData = $commentService->transformData($comments->all());

        $postData['comments'] = $commentsData;

        $response->json([
            'status' => 'success',
            'data' => $postData,
        ]);
    }
}