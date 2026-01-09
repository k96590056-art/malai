<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TronUsdtService;

/**
 * TRON区块链回调控制器
 * 
 * 处理docs.tronscan.org的回调请求，自动验证USDT充值
 */
class TronCallbackController extends Controller
{
    /**
     * 处理TRON USDT充值回调
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleUsdtRecharge(Request $request)
    {
        try {
            Log::info('收到TRON USDT充值回调:', $request->all());
            
            // 验证回调数据
            $callbackData = $request->all();
            
            if (empty($callbackData)) {
                return response()->json([
                    'success' => false,
                    'message' => '回调数据为空'
                ], 400);
            }
            
            // 验证必要字段
            $requiredFields = ['txid', 'address', 'amount', 'confirmations'];
            foreach ($requiredFields as $field) {
                if (!isset($callbackData[$field])) {
                    Log::warning('TRON回调缺少必要字段:', ['field' => $field, 'data' => $callbackData]);
                    return response()->json([
                        'success' => false,
                        'message' => "缺少必要字段: {$field}"
                    ], 400);
                }
            }
            
            // 使用TRON USDT服务处理回调
            $tronService = new TronUsdtService();
            $result = $tronService->handleCallback($callbackData);
            
            if ($result['success']) {
                Log::info('TRON回调处理成功:', [
                    'txid' => $callbackData['txid'],
                    'address' => $callbackData['address'],
                    'amount' => $callbackData['amount']
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => '回调处理成功'
                ]);
            } else {
                Log::warning('TRON回调处理失败:', [
                    'error' => $result['message'],
                    'data' => $callbackData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('TRON回调处理异常:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '系统错误: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 手动触发TRON交易验证（用于测试和调试）
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualVerify(Request $request)
    {
        try {
            $txHash = $request->input('tx_hash');
            $outTradeNo = $request->input('out_trade_no');
            
            if (empty($txHash) || empty($outTradeNo)) {
                return response()->json([
                    'success' => false,
                    'message' => '请提供交易哈希和订单号'
                ], 400);
            }
            
            // 使用TRON USDT服务验证交易
            $tronService = new TronUsdtService();
            $result = $tronService->verifyTransaction($txHash, $outTradeNo);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('手动验证TRON交易失败:', [
                'error' => $e->getMessage(),
                'tx_hash' => $request->input('tx_hash'),
                'out_trade_no' => $request->input('out_trade_no')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '验证失败: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 获取TRON网络状态信息
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNetworkStatus()
    {
        try {
            $apiUrl = config('services.tron.api_url', 'https://api.trongrid.io');
            $apiKey = config('services.tron.api_key');
            
            // 获取最新区块信息
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'TRON-PRO-API-KEY' => $apiKey
            ])->get($apiUrl . '/v1/blocks/latest');
            
            if (!$response->successful()) {
                throw new \Exception('TRON API请求失败: ' . $response->status());
            }
            
            $blockData = $response->json();
            
            $status = [
                'network' => 'TRON',
                'latest_block' => $blockData['block_header']['raw_data']['number'] ?? 0,
                'latest_block_time' => $blockData['block_header']['raw_data']['timestamp'] ?? 0,
                'api_status' => 'connected',
                'timestamp' => now()->toISOString()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
            
        } catch (\Exception $e) {
            Log::error('获取TRON网络状态失败:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '获取网络状态失败: ' . $e->getMessage()
            ], 500);
        }
    }
}
