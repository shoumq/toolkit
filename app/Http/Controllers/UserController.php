<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="ToolKit API"
 * )
 * @OA\SecurityScheme(
 *    securityScheme="bearerAuth",
 *    type="http",
 *    scheme="bearer"
 *)
 */
class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="All users",
     *     security={{"bearerAuth":{}}},
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="name", type="string", example="Андрей"),
     *                 @OA\Property(property="email_verified_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *                 @OA\Property(property="email", type="string", example="89999999999"),
     *                 @OA\Property(property="address", type="string", example="838 Destinee Lodge\nKuhicfort, OK 90655"),
     *                 @OA\Property(property="is_admin", type="boolean", example="false"),
     *            ),
     *          )
     *         )
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function all() : JsonResponse
    {
        return response()->json(User::all());
    }

    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Delete",
     *     security={{"bearerAuth":{}}},
     *     tags={"User"},
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent()
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function delete(int $id) : JsonResponse
    {
        return User::remove($id);
    }

    /**
     * @OA\Patch(
     *     path="/api/user/{id}",
     *     summary="Patch",
     *     security={{"bearerAuth":{}}},
     *     tags={"User"},
     *      @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *    ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", format="name"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="number", type="string", format="number"),
     *             @OA\Property(property="address", type="string", format="address"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent()
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function update(Request $request, int $id) : JsonResponse
    {
        return User::modify($request, $id);
    }
}
