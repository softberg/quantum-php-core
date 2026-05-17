<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace {{MODULE_NAMESPACE}}\Controllers\OpenApi;

/**
 * Class ApiController
 * @package Modules\Api
 * @OA\Info(
 *    title="Quantum API documentation",
 *    version="2.9.0",
 *    description=" *Quantum Documentation: https://quantumphp.io/en/docs/v1/overview"
 *  ),
 * @OA\SecurityScheme(
 *    securityScheme="bearer_token",
 *    type="apiKey",
 *    name="Authorization",
 *    in="header"
 *  )
 */
abstract class OpenApiController
{

}
