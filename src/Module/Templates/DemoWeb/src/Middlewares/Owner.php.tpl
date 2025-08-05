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

namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Service\Factories\ServiceFactory;
use Quantum\Libraries\Validation\Rule;
use Quantum\Http\Constants\StatusCode;
use Modules\{{MODULE_NAME}}\Services\PostService;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Editor
 * @package Modules\{{MODULE_NAME}}
 */
class Owner extends BaseMiddleware
{


    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        $uuid = (string)route_param('uuid');

        $request->set('uuid', $uuid);

        $this->validateRequest($request, $response);

        return $next($request, $response);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->registerCustomRules();

        $this->validator->addRules([
            'uuid' => [
                Rule::set('required'),
                Rule::set('post_owner'),
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message = null)
    {
        $response->html(partial('errors/404'),  StatusCode::NOT_FOUND);
        stop();
    }

    /**
     * Register custom validation rules
     */
    private function registerCustomRules()
    {
        $this->validator->addValidation('post_owner', function ($postUuid) {
            $post = ServiceFactory::get(PostService::class)->getPost($postUuid);
            return $post && !$post->isEmpty() && $post->user_uuid === auth()->user()->uuid;
        });
    }
}