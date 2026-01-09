<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Http\Controllers\AuthController as BaseAuthController;

class AuthController extends BaseAuthController
{
    // 如果需要自定义登录逻辑，可以在这里添加
    // 但不要重写getLogin方法，除非签名完全匹配
}
