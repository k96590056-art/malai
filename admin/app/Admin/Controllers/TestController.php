<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * 测试控制器 - 用于验证基本功能
 */
class TestController extends Controller
{
    /**
     * 基本测试方法
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'TestController 工作正常',
            'time' => date('Y-m-d H:i:s'),
            'controller' => get_class($this),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ]);
    }

    /**
     * 简单HTML测试
     */
    public function html()
    {
        return '<html><body><h1>TestController HTML 测试</h1><p>时间: ' . date('Y-m-d H:i:s') . '</p></body></html>';
    }

    /**
     * 错误测试
     */
    public function error()
    {
        try {
            throw new \Exception('这是一个测试异常');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
