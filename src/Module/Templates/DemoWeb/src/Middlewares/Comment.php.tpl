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

use Quantum\Libraries\Validation\Rule;
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
    public function apply(Request $request, Response $response, Closure $next)
    {
        $this->validateRequest($request, $response);
        return $next($request, $response);
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
    )
    {
        $data = $request->all();

        session()->setFlash('error', $this->validator->getErrors());
        redirectWith(get_referrer(), $data);
    }
}