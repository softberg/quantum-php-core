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
 * @since 3.0.0
 */

namespace {{MODULE_NAMESPACE}}\Middlewares;

use Quantum\Http\Enums\StatusCode;
use Quantum\Validation\Rule;
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

    public function apply(Request $request, Closure $next): Response
    {
        if (!in_array(auth()->user()->role, self::ROLES)) {
            return $this->respondWithError($request,
                t('validation.unauthorizedRequest'),
                StatusCode::UNAUTHORIZED
            );
        }

        if ($request->isMethod('post') || $request->isMethod('put')) {
            if ($errorResponse = $this->validateRequest($request)) {
                return $errorResponse;
            }
        }

        return $next($request);
    }

    /**
     * @inheritDoc
     */
    protected function defineValidationRules(Request $request): void
    {
        if ($request->hasFile('image')) {
            $this->validator->setRules([
                'image' => [
                    Rule::fileSize(self::MAX_IMAGE_SIZE_MB),
                    Rule::fileExtension(...self::ALLOWED_IMAGE_EXTENSIONS),
                ],
            ]);
        }

        $this->validator->setRules([
            'title' => [
                Rule::required(),
                Rule::minLen(10),
                Rule::maxLen(50),
            ],
            'content' => [
                Rule::required(),
                Rule::minLen(10),
                Rule::maxLen(1000),
            ],
        ]);
    }
}
