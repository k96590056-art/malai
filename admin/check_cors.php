<?php
/**
 * CORS配置检查脚本
 * 用于诊断CORS跨域问题
 */

require_once __DIR__ . '/vendor/autoload.php';

// 加载Laravel环境
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemConfig;

echo "=== CORS配置检查 ===\n\n";

// 检查CORS开关
$cors_enabled = SystemConfig::getValue('cors_enabled', '1');
echo "1. CORS开关状态: " . ($cors_enabled == '1' ? '✅ 已开启' : '❌ 已关闭') . "\n";

// 检查安全域名配置
$safe_domain = SystemConfig::getValue('safe_domain', '');
echo "2. 安全域名配置: " . ($safe_domain ? $safe_domain : '❌ 未配置') . "\n";

// 检查环境配置
echo "3. 应用环境: " . config('app.env') . "\n";
echo "4. 调试模式: " . (config('app.debug') ? '✅ 已开启' : '❌ 已关闭') . "\n";

// 检查API URL配置
echo "5. API URL: " . env('API_URL', '未配置') . "\n";
echo "6. 主站URL: " . env('APP_URL', '未配置') . "\n";

echo "\n=== 建议解决方案 ===\n";

if ($cors_enabled != '1') {
    echo "❌ CORS开关已关闭，请在管理后台开启CORS功能\n";
    echo "   路径：系统设置 -> 网站设置 -> CORS跨域开关\n\n";
}

if (empty($safe_domain)) {
    echo "❌ 未配置安全域名，请添加允许跨域的域名\n";
    echo "   路径：系统设置 -> 网站设置 -> 安全域名\n";
    echo "   格式：http://yourdomain.com,https://yourdomain.com\n\n";
}

echo "✅ 如果问题仍然存在，请检查：\n";
echo "   1. 确保API服务器和前端服务器域名配置正确\n";
echo "   2. 检查防火墙是否阻止了跨域请求\n";
echo "   3. 确认浏览器缓存已清除\n";
echo "   4. 检查Nginx/Apache配置是否正确\n";

echo "\n=== 当前请求头信息 ===\n";
if (isset($_SERVER['HTTP_ORIGIN'])) {
    echo "Origin: " . $_SERVER['HTTP_ORIGIN'] . "\n";
} else {
    echo "Origin: 未设置\n";
}

if (isset($_SERVER['HTTP_REFERER'])) {
    echo "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
} else {
    echo "Referer: 未设置\n";
}

echo "\n检查完成！\n";
?>
