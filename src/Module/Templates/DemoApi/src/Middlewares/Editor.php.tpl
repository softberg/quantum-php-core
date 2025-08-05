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

use Quantum\Http\Constants\StatusCode;
use Quantum\Libraries\Validation\Rule;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Editor
 * @package Modules\{{MODULE_NAME}}
 */
class Editor extends BaseMiddleware
{

    /**
     * Roles
     */
    const ROLES = ['admin', 'editor'];

    /**
     * Max image size in MB
     */
    private const MAX_IMAGE_SIZE_MB = 2 * 1024 * 1024;

    /**
     * Allowed image types
     */
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpeg', 'jpg', 'png'];

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if (!in_array(auth()->user()->role, self::ROLES)) {
            $this->respondWithError(
                $request,
                $response,
                t('validation.unauthorizedRequest'),
                StatusCode::UNAUTHORIZED
            );
        }

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $this->validateRequest($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request)
    {
        if ($request->hasFile('image')) {
            $this->validator->addRules([
                'image' => [
                    Rule::set('fileSize', self::MAX_IMAGE_SIZE_MB),
                    Rule::set('fileExtension', self::ALLOWED_IMAGE_EXTENSIONS),
                ],
            ]);
        }

        $this->validator->addRules([
            'title' => [
                Rule::set('required'),
                Rule::set('minLen', 10),
                Rule::set('maxLen', 50),
            ],
            'content' => [
                Rule::set('required'),
                Rule::set('minLen', 10),
                Rule::set('maxLen', 1000),
            ],
        ]);
    }
}