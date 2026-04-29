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

namespace Modules\Toolkit\Middlewares;

use Modules\Toolkit\Services\DatabaseService;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Validation\Rule;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class CreateTable
 * @package Modules\Toolkit
 */
class CreateTable extends BaseMiddleware
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
        $this->registerCustomRules();

        $originalTableName = $request->get('originalName');

        $this->validator->setRules([
            'table' => [
                Rule::required(),
                Rule::uniqueTableName($originalTableName)
            ],
            'data' => [
                Rule::required(),
                Rule::jsonString(),
                Rule::nonEmptyJson()
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function respondWithError(Request $request, Response $response, $message): Response
    {
        session()->setFlash('error', $message);
        return redirect(get_referrer() ?? base_url());
    }

    /**
     * Registers custom validation rules
     */
    private function registerCustomRules(): void
    {
        $this->validator->addRule('uniqueTableName', function ($value, $except = null) {
            $databaseService = ServiceFactory::get(DatabaseService::class);
            return !$databaseService->tableExists($value, $except);
        });

        $this->validator->addRule('nonEmptyJson', function ($value) {
            $decoded = json_decode($value, true);
            return is_array($decoded) && !empty($decoded);
        });
    }
}
