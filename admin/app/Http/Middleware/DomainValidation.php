<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SystemConfig;
use Illuminate\Http\Request;

class DomainValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $domain = SystemConfig::getValue('safe_domain');
        
        // 检查是否为后台管理域名，后台域名始终允许访问
        $adminUrl = config('app.url');
        $origin = $request->header('Origin') ?: $request->header('Referer');
        $isAdminDomain = !empty($origin) && strpos($origin, parse_url($adminUrl, PHP_URL_HOST)) !== false;
        
        // 如果是后台域名，直接通过验证
        if ($isAdminDomain) {
            return $next($request);
        }
        
        // 如果没有配置安全域名，直接通过验证
        if (empty($domain)) {
            return $next($request);
        }
        
        // 开发环境允许本地请求
        $isLocalRequest = in_array($origin, [
            'http://localhost:8080',
            'http://localhost:3000',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:3000'
        ]);
        
        // 如果是本地开发请求，直接通过
        if ($isLocalRequest) {
            return $next($request);
        }
        
        $allowedDomains = array_filter(array_map('trim', explode(',', $domain)));
        
        // 严格验证域名格式
        $allowedDomains = array_filter($allowedDomains, function($domain) {
            return filter_var($domain, FILTER_VALIDATE_URL) && 
                   in_array(parse_url($domain, PHP_URL_SCHEME), ['http', 'https']);
        });
        
        // 如果没有有效的允许域名，直接通过
        if (empty($allowedDomains)) {
            return $next($request);
        }
        
        // 检查Origin是否在允许的域名列表中
        if (!empty($origin)) {
            if (!in_array($origin, $allowedDomains)) {
                return response()->json(['code' => 401, 'message' => '域名未授权访问'], 401);
            }
        } else {
            // 如果没有Origin头，检查Referer
            $referer = $request->header('Referer');
            if (!empty($referer)) {
                $refererHost = parse_url($referer, PHP_URL_SCHEME) . '://' . parse_url($referer, PHP_URL_HOST);
                if (!in_array($refererHost, $allowedDomains)) {
                    return response()->json(['code' => 401, 'message' => '域名未授权访问'], 401);
                }
            } else {
                // 既没有Origin也没有Referer，拒绝访问
                return response()->json(['code' => 401, 'message' => '缺少来源信息'], 401);
            }
        }
        
        return $next($request);
    }
}
