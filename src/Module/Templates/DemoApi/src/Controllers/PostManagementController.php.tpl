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
use {{MODULE_NAMESPACE}}\Services\PostService;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class PostManagementController
 * @package Modules\{{MODULE_NAME}}
 */
class PostManagementController extends BaseController
{

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
     * Action - get my posts
     * @param Response $response
     */
    public function myPosts(Response $response)
    {
        $myPosts = $this->postService->getMyPosts(auth()->user()->uuid);
        
        $response->json([
            'status' => 'success',
            'data' => $this->postService->transformData($myPosts->all())
        ]);
    }

    /**
     * Action - create post
     * @param Request $request 
     * @param Response $response
     */
    public function create(Request $request, Response $response)
    {
        $postData = [
            'user_uuid' => auth()->user()->uuid,
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

        $response->json([
            'status' => 'success',
            'message' => t('common.created_successfully')
        ]);
    }

    /**
     * Action - amend post 
     * @param Request $request 
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     */
    public function amend(Request $request, Response $response, ?string $lang, string $postUuid)
    {
        $postData = [
            'title' => $request->get('title', null, true),
            'content' => $request->get('content', null, true),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $post = $this->postService->getPost($postUuid);

        if ($request->hasFile('image')) {
            if ($post->image) {
                $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
            }

            $imageName = $this->postService->saveImage(
                $request->getFile('image'),
                auth()->user()->uuid,
                slugify($request->get('title'))
            );

            $postData['image'] = $imageName;
        }

        $this->postService->updatePost($postUuid, $postData);

        $response->json([
            'status' => 'success',
            'message' => t('common.updated_successfully')
        ]);
    }

    /**
     * Action - delete post
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     */
    public function delete(Response $response, ?string $lang, string $postUuid)
    {
        $post = $this->postService->getPost($postUuid);

        if ($post->image) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
        }

        $this->postService->deletePost($postUuid);

        $response->json([
            'status' => 'success',
            'message' => t('common.deleted_successfully')
        ]);
    }

    /**
     * Action - delete image of the post
     * @param Response $response
     * @param string|null $lang 
     * @param string $postUuid
     */
    public function deleteImage(Response $response, ?string $lang, string $postUuid)
    {
        $post = $this->postService->getPost($postUuid);

        if ($post->image) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
        }

        $this->postService->updatePost($postUuid, [
            'title' => $post->title,
            'content' => $post->content,
            'image' => '',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $response->json([
            'status' => 'success',
            'message' => t('common.deleted_successfully')
        ]);
    }
}