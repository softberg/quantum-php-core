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

namespace {{MODULE_NAMESPACE}}\Controllers;

use Quantum\Service\Factories\ServiceFactory;
use Shared\Transformers\PostTransformer;
use Quantum\View\Factories\ViewFactory;
use Quantum\Http\Constants\StatusCode;
use Shared\Services\AuthService;
use Shared\Services\PostService;
use Quantum\View\RawParam;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class PostController
 * @package Modules\Web
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

    /**
     * Auth service
     * @var AuthService
     */
    public $userService;

    /**
     * Works before an action
     * @param ViewFactory $view
     */
    public function __before()
    {
        $this->postService = ServiceFactory::get(PostService::class);
        $this->userService = ServiceFactory::get(AuthService::class);

        parent::__before();
    }

    /**
     * Action - get posts list
     * @param Response $response
     * @param PostTransformer $transformer
     */
    public function posts(Request $request, Response $response, PostTransformer $transformer)
    {
        $perPage = $request->get('per_page', self::POSTS_PER_PAGE);
        $currentPage = $request->get('page', self::CURRENT_PAGE);
        $search = trim($request->get('q'));
        
        $paginatedPosts = $this->postService->getPosts($perPage, $currentPage, $search);

        $this->view->setParams([
            'title' => t('common.posts') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs'),
            'posts' => transform($paginatedPosts->data()->all(), $transformer),
            'pagination' => $paginatedPosts
        ]);

        $response->html($this->view->render('post/post'));
    }

    /**
     * Action - get single post
     * @param Request $request
     * @param Response $response
     * @param PostTransformer $transformer
     * @param string|null $lang
     * @param string $postId
     */
    public function post(Request $request, Response $response, PostTransformer $transformer, ?string $lang, string $postId)
    {
        $ref = $request->get('ref', 'posts');
    
        $post = $this->postService->getPost($postId);
        
        if ($post->isEmpty()) {
            $response->html(partial('errors/404'), StatusCode::NOT_FOUND);
            stop();
        }

        $this->view->setParams([
            'title' => $post->title . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs'),
            'post' => new RawParam(current(transform([$post], $transformer))),
            'referer' => $ref,
        ]);

        $response->html($this->view->render('post/single'));
    }

    /**
     * Action - get my posts
     * @param Request $request
     * @param Response $response
     */
    public function myPosts(Request $request, Response $response, PostTransformer $transformer)
    {
        $myPosts = $this->postService->getMyPosts((int)auth()->user()->id);

        $this->view->setParams([
            'title' => t('common.my_posts') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs'),
            'posts' => transform($myPosts->all(), $transformer)
        ]);

        $response->html($this->view->render('post/my-posts'));
    }

    /**
     * Action - display form for creating a post
     * @param Request $request
     * @param Response $response
     */
    public function createFrom(Request $request, Response $response)
    {
        $ref = $request->get('ref', 'posts');

        $this->view->setParams([
            'title' => t('common.new_post') . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs'),
            'referer' => $ref
        ]);

        $response->html($this->view->render('post/form'));
    }

    /**
     * Action - create post
     * @param Request $request
     */
    public function create(Request $request)
    {
        $postData = [
            'user_id' => (int)auth()->user()->id,
            'title' => $request->get('title', null, true),
            'content' => $request->get('content', null, true),
            'image' => '',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($request->hasFile('image')) {
            $imageName = $this->postService->saveImage(
                $request->getFile('image'),
                auth()->user()->uuid,
                slugify($request->get('title'))
            );

            $postData['image'] = $imageName;
        }

        $this->postService->addPost($postData);

        redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }

    /**
     * Action - display form for amend the post 
     * @param Request $request
     * @param Response $response
     * @param string|null $lang
     * @param string $postId
     */
    public function amendForm(Request $request, Response $response, ?string $lang, string $postId)
    {
        $ref = $request->get('ref', 'posts');

        $post = $this->postService->getPost($postId);

        $this->view->setParams([
            'title' => $post->title . ' | ' . config()->get('app_name'),
            'langs' => config()->get('langs'),
            'post' => $post->asArray(),
            'referer' => $ref
        ]);

        $response->html($this->view->render('post/form'));
    }

    /**
     * Action - amend post 
     * @param Request $request
     * @param string|null $lang
     * @param string $postId
     */
    public function amend(Request $request, ?string $lang, string $postId)
    {
        $postData = [
            'title' => $request->get('title', null, true),
            'content' => $request->get('content', null, true),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $post = $this->postService->getPost($postId);

        if ($request->hasFile('image')) {
            if ($post->image) {
                $this->postService->deleteImage(auth()->user()->uuid . DS .  $post->image);
            }

            $imageName = $this->postService->saveImage(
                $request->getFile('image'),
                auth()->user()->uuid,
                slugify($request->get('title'))
            );

            $postData['image'] = $imageName;
        }

        $this->postService->updatePost($postId, $postData);

        redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }

    /**
     * Action - delete post
     * @param string|null $lang
     * @param string $postId
     */
    public function delete(?string $lang, string $postId)
    {
        $post = $this->postService->getPost($postId);

        if ($post->image) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
        }

        $this->postService->deletePost($postId);

        redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }

    /**
     * Action - delete image of the post
     * @param string|null $lang
     * @param string $postId
     */
    public function deleteImage(?string $lang, string $postId)
    {
        $post = $this->postService->getPost($postId);

        if ($post->image) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
        }

        $this->postService->updatePost($postId, [
            'title' => $post->title,
            'content' => $post->content,
            'image' => '',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }
}