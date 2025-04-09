<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Xác thực",
 *     description="API cho đăng nhập và đăng xuất"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Đăng nhập người dùng",
     *     tags={"Xác thực"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="janesmith@example.com"),
     *             @OA\Property(property="password", type="string", example="securepassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Đăng nhập thành công!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="role", type="integer", example=1),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Thông tin đăng nhập không hợp lệ",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi đăng nhập"),
     *             @OA\Property(property="error", type="string", example="Email hoặc mật khẩu không chính xác")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi xác thực dữ liệu",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi xác thực"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
     *         )
     *     )
     * )
     */

     
     public function login(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'email' => 'required|email',
             'password' => 'required|string'
         ], [
             'email.required' => 'Email là bắt buộc.',
             'email.email' => 'Email không hợp lệ.',
             'password.required' => 'Mật khẩu là bắt buộc.',
             'password.string' => 'Mật khẩu phải là chuỗi ký tự.'
         ]);
     
         if ($validator->fails()) {
             return response()->json([
                 'status' => false,
                 'message' => 'Lỗi xác thực',
                 'errors' => $validator->errors()
             ], 422);
         }
     
         $credentials = $request->only('email', 'password');
     
         try {
             // Sử dụng JWTAuth thay vì Auth
             if (!$token = JWTAuth::attempt($credentials)) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Lỗi đăng nhập',
                     'error' => 'Email hoặc mật khẩu không chính xác'
                 ], 401);
             }
     
             $user = JWTAuth::user();
     
             return response()->json([
                 'status' => true,
                 'message' => 'Đăng nhập thành công!',
                 'data' => [
                     'access_token' => $token,
                     'token_type' => 'bearer',
                     'role' => $user->role,
                     'expires_in' => auth('api')->factory()->getTTL() * 60
                 ]
             ], 200);
         } catch (JWTException $e) {
             // Ghi log để debug
             \Log::error('Login JWT error: ' . $e->getMessage());
             return response()->json([
                 'status' => false,
                 'message' => 'Không thể tạo token',
                 'error' => $e->getMessage()
             ], 500);
         } catch (\Exception $e) {
             \Log::error('Login error: ' . $e->getMessage());
             return response()->json([
                 'status' => false,
                 'message' => 'Lỗi hệ thống',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Đăng xuất người dùng",
     *     tags={"Xác thực"},
     *     @OA\Response(
     *         response=200,
     *         description="Đăng xuất thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Đăng xuất thành công")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Chưa xác thực",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Chưa xác thực")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Không thể đăng xuất")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function logout()
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'status' => true,
                'message' => 'Đăng xuất thành công'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể đăng xuất'
            ], 500);
        }
    }
}