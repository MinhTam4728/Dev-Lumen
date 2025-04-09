<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
      
        if ($this->auth->guest()) {
            return response()->json(['message' => 'Unauthorized - Chưa đăng nhập'], 401);
        }

      
        $user = $this->auth->user();
        
     

      
        if ((int)$user->role !== (int)$role) {
            return response()->json([
                'message' => 'Vai trò không hợp lệ',
                'debug' => [
                    'user_role' => (int)$user->role,
                    'required_role' => (int)$role
                ]
            ], 403);
        }

        return $next($request);
    }
}