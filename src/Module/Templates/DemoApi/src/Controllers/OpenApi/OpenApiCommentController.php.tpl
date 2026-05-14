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

use Quantum\Http\Request;

/**
 * Class OpenApiCommentController
 * @package Modules\Api
 */
abstract class OpenApiCommentController extends OpenApiController
{
    /**
     * Create comment action
     * @OA\Post(
     *    path="/api/comments/create/{uuid}",
     *    tags={"Comments"},
     *    summary="Create comment action",
     *    operationId="createComment",
     *    security={{"bearer_token": {}}},
     *    @OA\Parameter(name="uuid", description="Post UUID", required=true, in="path", @OA\Schema(type="string")),
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *        mediaType="application/json",
     *        @OA\Schema(
     *          required={"content"},
     *          @OA\Property(property="content", type="string"),
     *          example={"content": "Great post"}
     *        )
     *      )
     *    ),
     *    @OA\Response(response=200, description="Success"),
     *    @OA\Response(response=401, description="Unauthorized Request"),
     *    @OA\Response(response=422, description="Unprocessable Entity"),
     *    @OA\Response(response=500, description="Internal Server Error")
     *  )
     */
    abstract public function create(Request $request, ?string $lang, string $uuid);

    /**
     * Delete comment action
     * @OA\Delete(
     *    path="/api/comments/delete/{uuid}",
     *    tags={"Comments"},
     *    summary="Delete comment action",
     *    operationId="deleteComment",
     *    security={{"bearer_token": {}}},
     *    @OA\Parameter(name="uuid", description="Comment UUID", required=true, in="path", @OA\Schema(type="string")),
     *    @OA\Response(response=200, description="Success"),
     *    @OA\Response(response=401, description="Unauthorized Request"),
     *    @OA\Response(response=422, description="Unprocessable Entity"),
     *    @OA\Response(response=500, description="Internal Server Error")
     *  )
     */
    abstract public function delete(?string $lang, string $uuid);
}
