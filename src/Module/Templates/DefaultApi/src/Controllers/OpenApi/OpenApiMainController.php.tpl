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

namespace {{MODULE_NAMESPACE}}\Controllers\OpenApi;

use Quantum\Http\Response;

/**
 * Class OpenApiPostController
 * @package Modules\Api
 */
abstract class OpenApiMainController extends OpenApiController
{
   
    /**
     * @OA\Get(
     *     path="/{{MODULE_NAME}}",
     *     tags={"{{MODULE_NAME}}"},
     *     summary="Get status of {{MODULE_NAME}}",
     *     description="Returns status of {{MODULE_NAME}} module.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="{{MODULE_NAME}} module.")
     *         )
     *     )
     * )
     */
    abstract public function index(Response $response);

}