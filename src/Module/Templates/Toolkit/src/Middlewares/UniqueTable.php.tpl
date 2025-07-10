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

namespace Modules\Toolkit\Middlewares;

use Modules\Toolkit\Services\DatabaseService;
use Quantum\Service\Factories\ServiceFactory;
use Quantum\Libraries\Validation\Validator;
use Quantum\Libraries\Validation\Rule;
use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class UniqueTable
 * @package Modules\Toolkit
 */
class UniqueTable extends QtMiddleware
{
    public function __construct(Request $request, Response $response)
    {
        $this->validator = new Validator();

        $this->validator->addValidation('uniqueTableName', function ($value, $except = null) {
            $databaseService = ServiceFactory::get(DatabaseService::class);
            return !$databaseService->tableExists($value, $except);
        });

        $originalTableName = $request->get('originalName');

        $this->validator->addRules([
            'table' => [
                Rule::set('required'),
                Rule::set('uniqueTableName', $originalTableName)
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if (!$this->validator->isValid($request->all())) {
            session()->setFlash('error', $this->validator->getErrors());
            redirect(get_referrer());
        }

        return $next($request, $response);
    }

}