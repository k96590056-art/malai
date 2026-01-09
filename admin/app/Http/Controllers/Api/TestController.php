<?php
//decode by http://www.yunlu99.com/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DbzhenrenService;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    /**
     * 测试获取游戏记录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        try {
            // 获取请求参数，如果没有提供则使用默认值
            $startTime = $request->input('start_time', date('Y-m-d H:i:s', strtotime('-30 minutes')));
            $endTime = $request->input('end_time', date('Y-m-d H:i:s'));
            $pageIndex = $request->input('page_index', 1);

            // 记录请求参数
            Log::info('TestController 测试获取游戏记录 - 请求参数', [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'page_index' => $pageIndex,
                'request_data' => $request->all()
            ]);

            // 创建 DbzhenrenService 实例
            $service = new DbzhenrenService();

            // 记录服务创建成功
            Log::info('TestController DbzhenrenService 实例创建成功');

            // 调用 getGameRecords 方法
            Log::info('TestController 开始调用 getGameRecords 方法', [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'page_index' => $pageIndex
            ]);

            $result = $service->getGameRecords($startTime, $endTime, $pageIndex);

            // 记录返回结果
            Log::info('TestController getGameRecords 调用完成', [
                'result_code' => $result['code'] ?? 'unknown',
                'result_message' => $result['message'] ?? '',
                'result_data_count' => is_array($result['data'] ?? null) ? count($result['data']) : 'N/A',
                'result' => $result
            ]);

            // 返回结果
            return $this->returnMsg(200, [
                'method' => 'getGameRecords',
                'params' => [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'page_index' => $pageIndex
                ],
                'result' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            ], '游戏记录获取完成，请查看日志获取详细信息');

        } catch (\Exception $e) {
            // 记录异常信息
            Log::error('TestController 获取游戏记录异常', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return $this->returnMsg(500, [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], '获取游戏记录失败：' . $e->getMessage());
        }
    }
}
