<?php

return '<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.0
 */

namespace Modules\\' . Quantum\Libraries\Module\ModuleManager::$moduleName . '\Controllers\OpenApi;

use Quantum\Http\Response;

/**
 * Class OpenApiPostController
 * @package Modules\Api
 */
abstract class OpenApiMainController extends OpenApiController
{
   
    /**
     * @OA\Get(
     *     path="/' . strtolower(Quantum\Libraries\Module\ModuleManager::$moduleName) . '",
     *     tags={"' . Quantum\Libraries\Module\ModuleManager::$moduleName . '"},
     *     summary="Get status of ' . Quantum\Libraries\Module\ModuleManager::$moduleName . '",
     *     description="Returns status of ' . Quantum\Libraries\Module\ModuleManager::$moduleName . ' module.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="' . Quantum\Libraries\Module\ModuleManager::$moduleName . ' module.")
     *         )
     *     )
     * )
     */
    abstract public function index(Response $response);

}';