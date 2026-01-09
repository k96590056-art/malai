<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class CrossHttp
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
        $lang = Request::header('Lang') ?? 'zh_CN';
        App::setLocale($lang);
        
        $response = $next($request);
        
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        
        // 验证 Origin 格式
        if (!empty($origin) && !filter_var($origin, FILTER_VALIDATE_URL)) {
            $origin = '';
        }
        
        // 检查是否为后台管理域名，后台域名始终允许访问
        $adminUrl = config('app.url');
        $isAdminDomain = !empty($origin) && strpos($origin, parse_url($adminUrl, PHP_URL_HOST)) !== false;
        
        // 检查CORS开关配置
        $cors_enabled = SystemConfig::getValue('cors_enabled', '1');
        
        // 如果CORS被禁用，只允许后台域名和配置的安全域名
        if ($cors_enabled != '1') {
            $domain = SystemConfig::getValue('safe_domain');
            $allowedDomains = [];
            
            if (!empty($domain)) {
                $allowedDomains = array_filter(array_map('trim', explode(',', $domain)));
                $allowedDomains = array_filter($allowedDomains, function($domain) {
                    return filter_var($domain, FILTER_VALIDATE_URL) && 
                           in_array(parse_url($domain, PHP_URL_SCHEME), ['http', 'https']);
                });
            }
            
            // 开发环境允许本地请求
            $isLocalRequest = in_array($origin, [
                'http://localhost:8080',
                'http://localhost:3000',
                'http://127.0.0.1:8080',
                'http://127.0.0.1:3000'
            ]);
            
            // 允许后台域名、配置的安全域名或本地开发请求
            if ($isAdminDomain || in_array($origin, $allowedDomains) || $isLocalRequest) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
                $response->headers->set('Access-Control-Max-Age', '86400');
            }
            return $response;
        }
        
        $domain = SystemConfig::getValue('safe_domain');
        
        // 开发环境允许本地请求
        $isLocalRequest = in_array($origin, [
            'http://localhost:8080',
            'http://localhost:3000',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:3000'
        ]);
        
        // 如果没有配置安全域名，允许所有来源的CORS请求
        if (empty($domain)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin ?: '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400'); // 24小时缓存
        } else {
            // 如果配置了安全域名，只允许配置的域名或本地开发请求
            $allowedDomains = array_filter(array_map('trim', explode(',', $domain)));
            
            // 严格验证域名格式
            $allowedDomains = array_filter($allowedDomains, function($domain) {
                return filter_var($domain, FILTER_VALIDATE_URL) && 
                       in_array(parse_url($domain, PHP_URL_SCHEME), ['http', 'https']);
            });
            
            // 允许配置的域名、后台域名或本地开发请求
            if (in_array($origin, $allowedDomains) || $isAdminDomain || $isLocalRequest) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
                $response->headers->set('Access-Control-Max-Age', '86400'); // 24小时缓存
            }
        }
        
        // 添加安全头
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        return $response;
    }
}
