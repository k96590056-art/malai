<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * 并发登录控制中间件
 * 
 * 防止同一用户在多处同时登录
 */
class ConcurrentLoginControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 只对需要认证的API请求进行控制
        if ($request->is('api/*') && $request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            
            if (!empty($token) && strlen($token) === 60) {
                $user = User::where('api_token', $token)->first();
                
                if ($user) {
                    $cacheKey = 'user_session_' . $user->id;
                    $currentSessionId = Cache::get($cacheKey);
                    
                    // 如果存在其他会话，检查是否过期
                    if ($currentSessionId && $currentSessionId !== $token) {
                        // 检查其他会话是否仍然有效
                        $otherUser = User::where('api_token', $currentSessionId)
                            ->where('token_expires_at', '>', now())
                            ->first();
                            
                        if ($otherUser) {
                            // 其他会话仍然有效，拒绝当前请求
                            return response()->json([
                                'code' => 401,
                                'message' => '检测到多处登录，请重新登录',
                                'data' => null
                            ], 401);
                        }
                    }
                    
                    // 更新当前会话
                    Cache::put($cacheKey, $token, now()->addDays(7));
                }
            }
        }

        return $next($request);
    }
}
