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

use Quantum\Service\Exceptions\ServiceException;
use Quantum\App\Exceptions\BaseException;
use {{MODULE_NAMESPACE}}\Services\PostService;
use {{MODULE_NAMESPACE}}\DTOs\PostDTO;
use Quantum\Di\Exceptions\DiException;
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
    public PostService $postService;

    public function __before()
    {
        $this->postService = service(PostService::class);
    }

    /**
     * Action - get my posts
     * @param Response $response
     */
    public function myPosts(Response $response): Response
    {
        $myPosts = $this->postService->getMyPosts(auth()->user()->uuid);
        
        return $response->json([
            'status' => 'success',
            'data' => $this->postService->transformData($myPosts->all())
        ]);
    }

    /**
     * Action - create post
     * @param Request $request 
     * @param Response $response
     */
    public function create(Request $request, Response $response): Response
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

        $post = $this->postService->addPost($postDto);

        $response->json([
            'status' => 'success',
            'message' => t('common.created_successfully'),
            'data' => current($this->postService->transformData([$post]))
        ]);
    }

    /**
     * Action - amend post 
     * @param Request $request 
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     */
    public function amend(Request $request, Response $response, ?string $lang, string $postUuid): Response
    {
        $post = $this->postService->getPost($postUuid);

        $imageName = null;

        if ($request->hasFile('image')) {
            if ($post->image) {
                $this->postService->deleteImage(auth()->user()->uuid . DS . $post->image);
    }

            $imageName = $this->postService->saveImage(
                $request->getFile('image'),
                auth()->user()->uuid,
                slugify($request->get('title'))
            );
        }

        $postDto = PostDTO::fromRequest($request, null, $imageName);

        $post = $this->postService->updatePost($postUuid, $postDto);

        $response->json([
            'status' => 'success',
            'message' => t('common.updated_successfully'),
            'data' => current($this->postService->transformData([$post]))
        ]);
    }

    /**
     * Action - delete post
     * @param Response $response
     * @param string|null $lang
     * @param string $postUuid
     */
    public function delete(Response $response, ?string $lang, string $postUuid): Response
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
    public function deleteImage(Response $response, ?string $lang, string $postUuid): Response
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

        $response->json([
            'status' => 'success',
            'message' => t('common.deleted_successfully')
        ]);
    }
}