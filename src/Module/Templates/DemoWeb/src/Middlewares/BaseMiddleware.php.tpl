<?php

namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Libraries\Validation\Validator;
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
     */
    protected function validateRequest(Request $request, Response $response)
    {
        if (!$this->validator->isValid($request->all())) {
            $this->respondWithError($request, $response, $this->validator->getErrors());
        }
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
     */
    protected function respondWithError(Request $request, Response $response, $message)
    {
        // default no-op: subclasses override if needed
    }
}