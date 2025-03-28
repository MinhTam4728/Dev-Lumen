<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


/**
 * @OA\Post(
 *     path="/login",
 *     summary="User Login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
*@OA\Property(property="email", type="string", example="admin123@gmail.com"),
 *             @OA\Property(property="password", type="string", example="123456789")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login successful"),
 *     @OA\Response(response=401, description="Invalid credentials"),
 *     @OA\Response(response=422, description="Validation error"),
 *   
 * )
 */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Lỗi đăng nhập',
                'error' => 'Email hoặc mật khẩu không chính xác'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'access_token' => $token,
            'token_type' => 'bearer',
            'role' => $user->role,
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="User Logout",
     *     @OA\Response(response=200, description="Logout successful"),
     *     @OA\Response(response=500, description="Could not logout")
     * )
     */
    public function logout()
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logout successful']);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not logout'], 500);
        }
    }
}