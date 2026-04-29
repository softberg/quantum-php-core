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

use {{MODULE_NAMESPACE}}\Services\PostService;
use {{MODULE_NAMESPACE}}\DTOs\PostDTO;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class PostManagementController
 * @package Modules\{{MODULE_NAME}}
 */
class PostManagementController extends BaseController
{
    
    /**
     * Main layout
     */
    protected const LAYOUT = 'layouts/main';
    
    /**
     * Post service
     * @var PostService
     */
    public PostService $postService;

    public function __before()
    {
        $this->postService = service(PostService::class);
        parent::__before();
    }

    /**
     * Action - get my posts
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function myPosts(Request $request, Response $response): Response
    {
        $myPosts = $this->postService->getMyPosts(auth()->user()->uuid);

        $this->view->setParams([
            'title' => t('common.my_posts') . ' | ' . config()->get('app.name'),
            'posts' => $this->postService->transformData($myPosts->all())
        ]);

        return $response->html($this->view->render('post/my-posts'));
    }

    /**
     * Action - display form for creating a post
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createFrom(Request $request, Response $response): Response
    {
        $ref = $request->get('ref', 'posts');

        $this->view->setParams([
            'title' => t('common.new_post') . ' | ' . config()->get('app.name'),
            'referer' => $ref
        ]);

        return $response->html($this->view->render('post/form'));
    }

    /**
     * Action - create post
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $imageName = '';

        if ($request->hasFile('image')) {
            $imageName = $this->postService->saveImage(
                $request->getFile('image'),
                auth()->user()->uuid,
                slugify($request->get('title'))
            );
        }

        $postDto = PostDTO::fromRequest($request, auth()->user()->uuid, $imageName);

        $this->postService->addPost($postDto);

        return redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }

    /**
     * Action - display form for amend the post 
     * @param Request $request
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     * @return Response
     */
    public function amendForm(Request $request, Response $response, ?string $lang, string $postUuid): Response
    {
        $ref = $request->get('ref', 'posts');

        $post = $this->postService->getPost($postUuid);

        $this->view->setParams([
            'title' => $post->title . ' | ' . config()->get('app.name'),
            'post' => $post->asArray(),
            'referer' => nav_ref_decode($ref)
        ]);

        return $response->html($this->view->render('post/form'));
    }

    /**
     * Action - amend post 
     * @param Request $request
     * @param string|null $lang
     * @param string $postUuid
     * @return Response
     */
    public function amend(Request $request, ?string $lang, string $postUuid): Response
    {
        $post = $this->postService->getPost($postUuid);

        $imageName = null;

        if ($request->hasFile('image')) {
            if ($post->image) {
                $this->postService->deleteImage(auth()->user()->uuid . DS .  $post->image);
            }

            $imageName = $this->postService->saveImage(
                $request->getFile('image'),
                auth()->user()->uuid,
                slugify($request->get('title'))
            );
        }

        $postDto = PostDTO::fromRequest($request, null, $imageName);

        $this->postService->updatePost($postUuid, $postDto);

        return redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }

    /**
     * Action - delete post
     * @param string|null $lang
     * @param string $postUuid
     * @return Response
     */
    public function delete(?string $lang, string $postUuid): Response
    {
        $post = $this->postService->getPost($postUuid);

        if ($post->image) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
        }

        $this->postService->deletePost($postUuid);

        return redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }

    /**
     * Action - delete image of the post
     * @param string|null $lang
     * @param string $postUuid
     * @return Response
     */
    public function deleteImage(?string $lang, string $postUuid): Response
    {
        $post = $this->postService->getPost($postUuid);

        if ($post->image) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
        }

        $postDto = new PostDTO(
            $post->title,
            $post->content,
            null,
            ''
        );

        $this->postService->updatePost($postUuid, $postDto);

        return redirect(base_url(true) . '/' . current_lang() . '/my-posts');
    }
}
