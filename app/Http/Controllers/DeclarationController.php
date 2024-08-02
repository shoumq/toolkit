<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Declaration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeclarationController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/declaration",
     *     summary="All declarations",
     *     security={{"bearerAuth":{}}},
     *     tags={"Declaration"},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="title", type="string", example="Доклад №1"),
     *                 @OA\Property(property="date", type="string", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="email_verified_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
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
    public function all()
    {
        return response()->json(Declaration::all());
    }

    /**
     * @OA\Post(
     *     path="/api/declaration",
     *     summary="Declaration",
     *     tags={"Declaration"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="title", type="string", format="name"),
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
    public function create(Request $request)
    {
        $request->validate([
            'title' => ['string', 'required']
        ]);

        $input = $request->all();
        $declaration = Declaration::create($input);

        $redisKey = 'declaration:' . $declaration->id;

        Cache::set($redisKey, json_encode($declaration));
        return $this->sendResponse($declaration, "1");
    }

    /**
     * @OA\Delete(
     *     path="/api/declaration/{id}",
     *     summary="Delete",
     *     security={{"bearerAuth":{}}},
     *     tags={"Declaration"},
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
    public function delete($id)
    {
        $declaration = Declaration::findOrFail($id);
        $declaration->delete();

        Cache::forget("declaration:" . $id);

        return response()->json(['message' => 'Declaration deleted successfully.']);
    }

    /**
     * @OA\Patch(
     *     path="/api/declaration/{id}",
     *     summary="Patch",
     *     security={{"bearerAuth":{}}},
     *     tags={"Declaration"},
     *      @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *    ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", format="name"),
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
    public function update(Request $request, $id)
    {
        $declaration = Declaration::findOrFail($id);
        $declaration->update($request->all());

        Cache::put("declaration:{$id}", $declaration);

        return response()->json(['message' => 'Declaration updated successfully.', 'declaration' => $declaration]);
    }
}
