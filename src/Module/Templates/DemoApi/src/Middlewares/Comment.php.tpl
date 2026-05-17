<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Validation\Rule;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Comment
 * @package Modules\{{MODULE_NAME}}
 */
class Comment extends BaseMiddleware
{
    public function apply(Request $request, Closure $next): Response
    {
        if ($errorResponse = $this->validateRequest($request)) {
            return $errorResponse;
        }

        return $next($request);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request): void
    {
        $this->validator->setRules([
            'content' => [
                Rule::required(),
                Rule::minLen(2),
                Rule::maxLen(100),
            ],
        ]);
    }
}
