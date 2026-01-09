<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | 安全配置设置
    |
    */

    // 密码策略
    'password' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', false),
    ],

    // 登录安全
    'login' => [
        'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
        'lockout_time' => env('LOGIN_LOCKOUT_TIME', 300), // 5分钟
        'throttle' => env('LOGIN_THROTTLE', '60,1'), // 每分钟最多1次
    ],

    // API 安全
    'api' => [
        'token_length' => env('API_TOKEN_LENGTH', 60),
        'token_expiry' => env('API_TOKEN_EXPIRY', 86400), // 24小时
        'rate_limit' => env('API_RATE_LIMIT', '60000,1'), // 每分钟最多1次
    ],

    // 跨域安全
    'cors' => [
        'allow_credentials' => env('CORS_ALLOW_CREDENTIALS', false),
        'max_age' => env('CORS_MAX_AGE', 86400),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allowed_headers' => [
            'Origin', 'Content-Type', 'Accept', 'Authorization', 
            'X-Requested-With', 'X-CSRF-TOKEN'
        ],
    ],

    // 安全头
    'headers' => [
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
    ],

    // 文件上传安全
    'uploads' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'scan_virus' => env('UPLOAD_SCAN_VIRUS', true),
    ],

    // 会话安全
    'session' => [
        'secure' => env('SESSION_SECURE', true),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
        'lifetime' => env('SESSION_LIFETIME', 120),
    ],
];
