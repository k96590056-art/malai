<?php
//decode by http://www.yunlu99.com/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * 测试方法
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        return $this->returnMsg(200, [
            'message' => 'Test successful',
            'timestamp' => date('Y-m-d H:i:s'),
            'request_data' => $request->all()
        ], '测试成功');
    }
}
