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

use Quantum\Libraries\Validation\Validator;
use Quantum\Http\Constants\StatusCode;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class BaseMiddleware
 * @package Modules\{{MODULE_NAME}}
 */
abstract class BaseMiddleware extends QtMiddleware
{

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * BaseMiddleware constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->validator = new Validator();

        $this->defineValidationRules($request);
    }

    /**
     * Define validation rules specific to middleware.
     * @param Request $request
     */
    protected function defineValidationRules(Request $request)
    {
        // default no-op: subclasses override if needed
    }

    /**
     * Validate the request and respond with error if invalid.
     * @param Request $request
     * @param Response $response
     */
    protected function validateRequest(Request $request, Response $response)
    {
        if (!$this->validator->isValid($request->all())) {
            $this->respondWithError($request, $response, $this->validator->getErrors());
        }
    }

    /**
     * Handles error response logic.
     * @param Request $request
     * @param Response $response
     * @param mixed $message
     * @param int $status
     */
    protected function respondWithError(
        Request $request,
        Response $response,
        $message,
        int $status = StatusCode::UNPROCESSABLE_ENTITY
    )
    {
        $response->json([
            'status' => 'error',
            'message' => $message,
        ], $status);

        stop();
    }
}
