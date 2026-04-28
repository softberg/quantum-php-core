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

use {{MODULE_NAMESPACE}}\Services\CommentService;
use Quantum\Validation\Rule;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class CommentOwner
 * @package Modules\{{MODULE_NAME}}
 */
class CommentOwner extends BaseMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return Response
     */
    public function apply(Request $request, Closure $next): Response
    {
        $response = response();
        $uuid = (string)route_param('uuid');

        $request->set('uuid', $uuid);

        if ($errorResponse = $this->validateRequest($request, $response)) {
            return $errorResponse;
        }

        return $next($request);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request): void
    {
        $this->registerCustomRules();

        $this->validator->setRules([
            'uuid' => [
                Rule::required(),
                Rule::commentOwner(),
            ],
        ]);
    }

    /**
     * Registers custom validation rules
     */
    private function registerCustomRules(): void
    {
        $this->validator->addRule('commentOwner', function ($commentUuid) {
            $comment = service(CommentService::class)->getComment($commentUuid);
            return !$comment->isEmpty() && $comment->user_uuid === auth()->user()->uuid;
        });
    }
}


