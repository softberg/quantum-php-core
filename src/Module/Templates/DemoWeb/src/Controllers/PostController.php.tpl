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
use Quantum\Http\Constants\StatusCode;
use {{MODULE_NAMESPACE}}\Services\PostService;
use Quantum\View\RawParam;
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
     * Main layout
     */
    const LAYOUT = 'layouts/main';
    
    /**
     * Post service
     * @var PostService
     */
    public $postService;

    public function __before()
    {
        $this->postService = ServiceFactory::create(PostService::class);
        parent::__before();
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

        $this->view->setParams([
            'title' => t('common.posts') . ' | ' . config()->get('app.name'),
            'posts' => $this->postService->transformData($paginatedPosts->data()->all()),
            'pagination' => $paginatedPosts
        ]);

        $response->html($this->view->render('post/post'));
    }

    /**
     * Action - get single post
     * @param Request $request
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     */
    public function post(Request $request, Response $response, ?string $lang, string $postUuid)
    {
        $ref = $request->get('ref', 'posts');
    
        $post = $this->postService->getPost($postUuid);
        
        if ($post->isEmpty()) {
            $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
            stop();
        }

        $commentService = ServiceFactory::create(CommentService::class);
        $comments = $commentService->getCommentsByPost($postUuid);

        $comments = $commentService->transformData($comments->all());

        $this->view->setParams([
            'title' => $post->title . ' | ' . config()->get('app.name'),
            'post' => new RawParam(current($this->postService->transformData([$post]))),
            'comments' => $comments,
            'referer' => $ref,
        ]);

        $response->html($this->view->render('post/single'));
    }
}