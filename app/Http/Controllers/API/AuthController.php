<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthController extends BaseController
{

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register",
     *     tags={"Auth"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="number", type="string", nullable=true),
     *             @OA\Property(property="address", type="string", nullable=true)
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
    public function register(UserRequest $request)
    {
        $input = $request->all();
        if (!isset($input['password'])) {
            return response()->json(['error' => 'Password is required.'], 400);
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['user'] = $user;

        return $this->sendResponse($user, 'User register successfully.');
    }


    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login",
     *     tags={"Auth"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
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
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }

        $success = $this->respondWithToken($token);

        return $this->sendResponse($success, 'User login successfully.');
    }

   /**
     * @OA\Post(
     *     path="/api/auth/profile",
     *     summary="Profile",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
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
    public function profile()
    {
        $user = auth()->user();
        $redisKey = 'user:' . $user->id;
        $cachedUser = Cache::get($redisKey);

        if (!$cachedUser) {
            $success = $user;
            Cache::set($redisKey, json_encode($success));
            return $this->sendResponse($success, "1");
        }
        $success = json_decode($cachedUser);
        return $this->sendResponse($success, "2");
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
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
    public function logout()
    {
        Cache::forget('user:' . auth()->user()->id);
        auth()->logout();

        return $this->sendResponse([], 'Successfully logged out.');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
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
    public function refresh()
    {
        Cache::forget('user:' . auth()->user()->id);
        $user = auth()->user();
        $redisKey = 'user:' . $user->id;
        Cache::set($redisKey, json_encode($user));

        $success = $this->respondWithToken(auth()->refresh());

        return $this->sendResponse($success, 'Refresh token return successfully.');
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
