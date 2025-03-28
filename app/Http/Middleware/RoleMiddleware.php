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
        // Check if user is authenticated
        if ($this->auth->guest()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get authenticated user
        $user = $this->auth->user();
        
        // Log for debugging
        Log::info('Role Check', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_role' => $role
        ]);

        // Compare roles as integers
        if ((int)$user->role !== (int)$role) {
            return response()->json([
                'message' => 'Forbidden - Invalid role',
                'debug' => [
                    'user_role' => (int)$user->role,
                    'required_role' => (int)$role
                ]
            ], 403);
        }

        return $next($request);
    }
}