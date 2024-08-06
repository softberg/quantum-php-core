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

namespace Modules\\' . $this->moduleName . '\Controllers\Abstracts;

use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Class OpenApiPostController
 * @package Modules\Api
 */
abstract class OpenApiMainController extends ApiController
{

    /**
     * @OA\Info(
     *     title="' . $this->moduleName . '",
     *     version="1.0.0",
     *     description="This is the ' . $this->moduleName . ' module."
     * )
     */
    
    /**
     * @OA\Tag(
     *     name="' . $this->moduleName . '",
     *     description="Operations about the ' . $this->moduleName . '"
     * )
     */
    
    /**
     * @OA\Get(
     *     path="/' . strtolower($this->moduleName) . '",
     *     tags={"' . $this->moduleName . '"},
     *     summary="Get status of ' . $this->moduleName . '",
     *     description="Returns status of ' . $this->moduleName . ' module.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="' . $this->moduleName . ' module.")
     *         )
     *     )
     * )
     */
    abstract public function index(Response $response);

}
';