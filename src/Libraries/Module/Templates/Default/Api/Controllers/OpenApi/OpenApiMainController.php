<?php

use Quantum\Libraries\Module\ModuleManager;

$moduleManager = ModuleManager::getInstance();

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
 * @since 2.9.5
 */

namespace '. $moduleManager->getBaseNamespace() .'\\' . $moduleManager->getModuleName() . '\Controllers\OpenApi;

use Quantum\Http\Response;

/**
 * Class OpenApiPostController
 * @package Modules\Api
 */
abstract class OpenApiMainController extends OpenApiController
{
   
    /**
     * @OA\Get(
     *     path="/' . strtolower($moduleManager->getModuleName()) . '",
     *     tags={"' . $moduleManager->getModuleName() . '"},
     *     summary="Get status of ' . $moduleManager->getModuleName() . '",
     *     description="Returns status of ' .$moduleManager->getModuleName() . ' module.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="' . $moduleManager->getModuleName() . ' module.")
     *         )
     *     )
     * )
     */
    abstract public function index(Response $response);

}';