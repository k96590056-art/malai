<?php

namespace App\Http\Middleware;
use Closure;
use \Illuminate\Http\Request;  
use \Illuminate\Support\Facades\DB;
use App\Models\User;

class Apiauthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 放行预检请求，返回 200
        if ($request->isMethod('OPTIONS')) {
            return response('', 200);
        }

        $token = $request->header('Authorization');
        
        if (empty($token)) {
            return response()->json(['code' => 401, 'message' => '认证失败'], 401);
        }
        
        // 移除 Bearer 前缀
        $token = str_replace('Bearer ', '', $token);
        
        // 验证 token 格式
        if (empty($token) || strlen($token) !== 60) {
            return response()->json(['code' => 401, 'message' => '认证失败'], 401);
        }
        
        // 使用 Eloquent 模型和参数化查询，避免 SQL 注入
        $user = User::where('api_token', $token)->first();
        
        if (empty($user)) {
            return response()->json(['code' => 401, 'message' => '认证失败'], 401);
        }
        
        // 将用户信息添加到请求中，供后续使用
        $request->merge(['current_user' => $user]);
        
        return $next($request);
    }
}
