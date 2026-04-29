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
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Comment
 * @package Modules\{{MODULE_NAME}}
 */
class Comment extends BaseMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     */
    public function apply(Request $request, Closure $next): Response
    {
        $response = response();
        if ($errorResponse = $this->validateRequest($request, $response)) {
            return $errorResponse;
        }

        return $next($request);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        $this->validator->setRules([
            'content' => [
                Rule::required(),
                Rule::minLen(2),
                Rule::maxLen(100),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(
        Request $request,
        Response $response,
        $message
    ): Response
    {
        $data = $request->all();

        session()->setFlash('error', $this->validator->getErrors());
        return redirectWith(get_referrer() ?? base_url(), $data);
    }
}
