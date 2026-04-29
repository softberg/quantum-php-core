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

use Quantum\Validation\Validator;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;

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
     * Initialize Validator and define rules.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->validator = new Validator();

        $this->defineValidationRules($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response|null
     */
    protected function validateRequest(Request $request, Response $response): ?Response
    {
        if (!$this->validator->isValid($request->all())) {
            return $this->respondWithError($request, $response, $this->validator->getErrors());
        }

        return null;
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
     * Handles error response logic.
     * @param Request $request
     * @param Response $response
     * @param mixed $message
     * @return Response
     */
    protected function respondWithError(Request $request, Response $response, $message): Response
    {
        // default no-op: subclasses override if needed
        return $response;
    }
}
