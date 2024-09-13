<?php

return '<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.0
 */

namespace Modules\Web\Controllers;

use Quantum\Factory\ServiceFactory;
use Quantum\Factory\ViewFactory;
use Shared\Services\AuthService;
use Shared\Services\PostService;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class PostController
 * @package Modules\Web\Controllers
 */
class PostController extends BaseController
{

    /**
     * Auth layout
     */
    const LAYOUT = \'layouts/main\';
    
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
    public function __before(ViewFactory $view)
    {
        $this->postService = ServiceFactory::get(PostService::class);
        $this->userService = ServiceFactory::get(AuthService::class);

        parent::__before($view);
    }

    /**
     * Action - get posts list
     * @param Response $response
     * @param ViewFactory $view
     */
    public function posts(Request $request, Response $response, ViewFactory $view)
    {
        $posts = $this->postService->getPosts($request);
        $view->setParams([
            \'title\' => t(\'common.posts\') . \' | \' . config()->get(\'app_name\'),
            \'langs\' => config()->get(\'langs\'),
            \'posts\' => $posts->data,
            \'pagination\' => $posts
        ]);

        $response->html($view->render(\'post/post\'));
    }

    /**
     * Action - get single post
     * @param string|null $lang
     * @param string $postId
     * @param Response $response
     * @param ViewFactory $view
     */
    public function post(Response $response, ViewFactory $view, ?string $lang, string $postId)
    {
        $post = $this->postService->getPost($postId);

        $view->setParams([
            \'title\' => $post[\'title\'] . \' | \' . config()->get(\'app_name\'),
            \'langs\' => config()->get(\'langs\'),
            \'post\' => $post
        ]);

        $response->html($view->render(\'post/single\'));
    }

    /**
     * Action - get my posts
     * @param Request $request
     * @param Response $response
     * @param ViewFactory $view
     */
    public function myPosts(Request $request, Response $response, ViewFactory $view)
    {
        $view->setParams([
            \'title\' => t(\'common.my_posts\') . \' | \' . config()->get(\'app_name\'),
            \'langs\' => config()->get(\'langs\'),
            \'posts\' => $this->postService->getMyPosts((int)auth()->user()->id)
        ]);

        $response->html($view->render(\'post/my-posts\'));
    }

    /**
     * Action - display form for creating a post
     * @param Response $response
     * @param ViewFactory $view
     */
    public function createFrom(Response $response, ViewFactory $view)
    {
        $view->setParams([
            \'title\' => t(\'common.new_post\') . \' | \' . config()->get(\'app_name\'),
            \'langs\' => config()->get(\'langs\')
        ]);

        $response->html($view->render(\'post/form\'));
    }

    /**
     * Action - create post
     * @param Request $request
     */
    public function create(Request $request)
    {
        $postData = [
            \'user_id\' => (int)auth()->user()->id,
            \'title\' => $request->get(\'title\', null, true),
            \'content\' => $request->get(\'content\', null, true),
            \'image\' => \'\',
            \'updated_at\' => date(\'Y-m-d H:i:s\'),
        ];

        if ($request->hasFile(\'image\')) {
            $imageName = $this->postService->saveImage(
                $request->getFile(\'image\'),
                auth()->user()->uuid,
                slugify($request->get(\'title\'))
            );

            $postData[\'image\'] = $imageName;
        }

        $this->postService->addPost($postData);

        redirect(base_url(true) . \'/\' . current_lang() . \'/my-posts\');
    }

    /**
     * Action - display form for amend the post 
     * @param Response $response
     * @param ViewFactory $view
     * @param sting|null $lang
     * @param string $postId
     */
    public function amendForm(Response $response, ViewFactory $view, ?string $lang, string $postId)
    {
        $post = $this->postService->getPost($postId);

        $view->setParams([
            \'title\' => $post[\'title\'] . \' | \' . config()->get(\'app_name\'),
            \'langs\' => config()->get(\'langs\'),
            \'post\' => $post
        ]);

        $response->html($view->render(\'post/form\'));
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
            \'title\' => $request->get(\'title\', null, true),
            \'content\' => $request->get(\'content\', null, true),
            \'updated_at\' => date(\'Y-m-d H:i:s\'),
        ];

        $post = $this->postService->getPost($postId, false);

        if ($request->hasFile(\'image\')) {
            if ($post[\'image\']) {
                $this->postService->deleteImage(auth()->user()->uuid . DS .  $post[\'image\']);
            }

            $imageName = $this->postService->saveImage(
                $request->getFile(\'image\'),
                auth()->user()->uuid,
                slugify($request->get(\'title\'))
            );

            $postData[\'image\'] = $imageName;
        }

        $this->postService->updatePost($postId, $postData);

        redirect(base_url(true) . \'/\' . current_lang() . \'/my-posts\');
    }

    /**
     * Action - delete post
     * @param string|null $lang
     * @param string $postId
     */
    public function delete(?string $lang, string $postId)
    {
        $post = $this->postService->getPost($postId, false);

        if ($post[\'image\']) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post[\'image\']);
        }

        $this->postService->deletePost($postId);

        redirect(base_url(true) . \'/\' . current_lang() . \'/my-posts\');
    }

    /**
     * Action - delete image of the post
     * @param string|null $lang
     * @param string $postId
     */
    public function deleteImage(?string $lang, string $postId)
    {
        $post = $this->postService->getPost($postId, false);

        if ($post[\'image\']) {
            $this->postService->deleteImage(auth()->user()->uuid . DS . $post[\'image\']);
        }

        $this->postService->updatePost($postId, [
            \'title\' => $post[\'title\'],
            \'content\' => $post[\'content\'],
            \'image\' => \'\',
            \'updated_at\' => date(\'Y-m-d H:i:s\'),
        ]);

        redirect(base_url(true) . \'/\' . current_lang() . \'/my-posts\');
    }
}';