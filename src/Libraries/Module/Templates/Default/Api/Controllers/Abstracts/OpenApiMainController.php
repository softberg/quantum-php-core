<?php
use Quantum\Libraries\Module\ModuleManager;

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

namespace Modules\\' . ModuleManager::$moduleName . '\Controllers\Abstracts;

use Quantum\Http\Response;

/**
 * Class OpenApiPostController
 * @package Modules\Api
 */
abstract class OpenApiMainController extends ApiController
{

    /**
     * @OA\Info(
     *     title="' . ModuleManager::$moduleName . '",
     *     version="1.0.0",
     *     description="This is the ' . ModuleManager::$moduleName . ' module."
     * )
     */
    
    /**
     * @OA\Tag(
     *     name="' . ModuleManager::$moduleName . '",
     *     description="Operations about the ' . ModuleManager::$moduleName . '"
     * )
     */
    
    /**
     * @OA\Get(
     *     path="/' . strtolower(ModuleManager::$moduleName) . '",
     *     tags={"' . ModuleManager::$moduleName . '"},
     *     summary="Get status of ' . ModuleManager::$moduleName . '",
     *     description="Returns status of ' . ModuleManager::$moduleName . ' module.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="' . ModuleManager::$moduleName . ' module.")
     *         )
     *     )
     * )
     */
    abstract public function index(Response $response);

}
';