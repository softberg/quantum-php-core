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

namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Validation\Rule;
use Quantum\Http\Enums\StatusCode;
use {{MODULE_NAMESPACE}}\Services\PostService;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class PostOwner
 * @package Modules\{{MODULE_NAME}}
 */
class PostOwner extends BaseMiddleware
{


    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return Response
     */
    public function apply(Request $request, Response $response, Closure $next): Response
    {
        $uuid = (string)route_param('uuid');

        $request->set('uuid', $uuid);

        if ($errorResponse = $this->validateRequest($request, $response)) {
            return $errorResponse;
        }

        return $next($request, $response);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->registerCustomRules();

        $this->validator->setRules([
            'uuid' => [
                Rule::required(),
                Rule::postOwner(),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message = null): Response
    {
        return $response->html(partial('errors/404'),  StatusCode::NOT_FOUND);
    }

    /**
     * Register custom validation rules
     */
    private function registerCustomRules()
    {
        $this->validator->addRule('postOwner', function ($postUuid) {
            $post = service(PostService::class)->getPost($postUuid);
            return !$post->isEmpty() && $post->user_uuid === auth()->user()->uuid;
        });
    }
}