<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;
use App\Models\User_Api;
use Illuminate\Http\Request;

/**
 * DbzhenrenService 真人游戏接口处理类
 * 参考文档：zhenren.md
 * 
 * 注意：此类包含两部分功能：
 * 1. 主动调用API（创建账号、进入游戏、查询数据等）
 * 2. 处理真人平台发起的回调请求（签名验证、回调响应格式化等）
 */
class DbzhenrenService
{
    protected $db_code;
    protected $merchant_code;
    protected $secret_key; // MD5签名密钥
    protected $aes_key; // AES加密密钥
    protected $api_url;
    protected $api_data_url;
    protected $md5_key;

    public function __construct()
    {
        // 从系统配置获取接口相关配置
        $this->db_code = "DBZR";
        $this->api_url = SystemConfig::getValue('dbzhenren_api_url') ?? env('DBZHENREN_API_URL', '');
        $this->api_data_url = SystemConfig::getValue('dbzhenren_api_data_url') ?? env('DBZHENREN_API_DATA_URL', '');
        $this->merchant_code = SystemConfig::getValue('dbzhenren_merchant_code') ?? env('DBZHENREN_MERCHANT_CODE', '');
        $this->secret_key = SystemConfig::getValue('dbzhenren_secret_key') ?? env('DBZHENREN_SECRET_KEY', '');
        $this->aes_key = SystemConfig::getValue('dbzhenren_aes_key') ?? env('DBZHENREN_AES_KEY', '');
        $this->md5_key = "pb4lKiYPgD3LhFQH";
    }

    /**
     * 验证回调请求签名
     * 签名算法：MD5("业务原文JSON+MD5盐值")
     * 
     * @param string $paramsJson 业务参数JSON字符串（params字段的值）
     * @param string $signature 签名值（需要转成大写后比较）
     * @return bool
     */
    public function verifySign($paramsJson, $signature)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbzhenren 密钥未配置');
            return false;
        }

        // 生成签名：MD5(业务原文JSON + MD5盐值)
        $signString = $paramsJson . $this->secret_key;
        $calculatedSignature = strtoupper(md5($signString));

        // 比较签名（都转成大写）
        $signature = strtoupper($signature);

        $isValid = ($calculatedSignature === $signature);

        if (!$isValid) {
            Log::warning('Dbzhenren 签名验证失败', [
                'calculated' => $calculatedSignature,
                'received' => $signature,
                'params_length' => strlen($paramsJson)
            ]);
        }

        return $isValid;
    }

    /**
     * 生成回调响应签名
     * 签名算法：MD5("业务原文JSON+MD5盐值")
     * 
     * @param string $dataJson 响应数据JSON字符串
     * @return string MD5签名（大写）
     */
    public function generateSign($dataJson)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbzhenren 密钥未配置');
            return '';
        }

        // 生成签名：MD5(业务原文JSON + MD5盐值)
        $signString = $dataJson . $this->secret_key;
        return strtoupper(md5($signString));
    }

    /**
     * 生成使用 md5_key 的签名
     * 签名算法：MD5("业务原文JSON + $this->md5_key")
     * 
     * @param string $dataJson 响应数据JSON字符串
     * @return string MD5签名（大写）
     */
    private function generateMd5KeySign($dataJson)
    {
        if (empty($this->md5_key)) {
            Log::error('Dbzhenren md5_key 未配置');
            return '';
        }

        // 生成签名：MD5(业务原文JSON + md5_key)
        $signString = $dataJson . $this->md5_key;
        $signature = md5($signString);
        
        Log::info('Dbzhenren 生成md5_key签名', [
            'data_json' => $dataJson,
            'data_json_length' => strlen($dataJson),
            'sign_string' => $signString,
            'signature' => strtoupper($signature)
        ]);
        
        return strtoupper($signature);
    }

    /**
     * 格式化成功响应
     * 
     * @param array $data 响应数据
     * @return array
     */
    public function formatSuccess($data)
    {
        // 将data转为JSON字符串（不转义Unicode和斜杠）
        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // 对data参数进行签名
        $signature = $this->generateSign($dataJson);

        return [
            'code' => 200,
            'message' => 'Success',
            'data' => $dataJson,
            'signature' => $signature
        ];
    }

    /**
     * 格式化失败响应
     * 
     * @param int|string $code 错误代码
     * @param string $message 错误消息
     * @return array
     */
    public function formatError($code, $message)
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }

    /**
     * 验证 params 字段的 signature 签名
     * 签名算法：MD5("params字段的值 + $this->md5_key")
     * 
     * @param string $params params字段的值（可能是JSON字符串或其他格式）
     * @param string $signature 客户端传入的签名
     * @return bool
     */
    private function verifyParamsSignature($params, $signature)
    {
        if (empty($signature)) {
            Log::warning('Dbzhenren signature 为空');
            return false;
        }

        if (empty($this->md5_key)) {
            Log::error('Dbzhenren md5_key 未配置');
            return false;
        }

        if (empty($params)) {
            Log::warning('Dbzhenren params 为空');
            return false;
        }

        // 计算签名：MD5(params字段的值 + md5_key)
        $signString = $params . $this->md5_key;
        $calculatedSignature = md5($signString);

        // 比较签名（不区分大小写）
        $isValid = (strtolower($calculatedSignature) === strtolower($signature));

        if (!$isValid) {
            Log::warning('Dbzhenren params signature 签名验证失败', [
                'calculated_signature' => $calculatedSignature,
                'received_signature' => $signature,
                'params' => $params,
                'params_length' => strlen($params),
                'sign_string' => $signString
            ]);
        } else {
            Log::info('Dbzhenren params signature 签名验证成功', [
                'params' => $params,
                'params_length' => strlen($params)
            ]);
        }

        return $isValid;
    }

    /**
     * 处理 getBalance 单个会员余额查询回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @return string JSON 格式字符串
     */
    public function getBalance()
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren getBalance 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren getBalance 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren getBalance params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $loginName = $paramsData['loginName'] ?? $request->input('loginName', '');
        $currency = $paramsData['currency'] ?? $request->input('currency', 'CNY');

        if (empty($loginName)) {
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90000,
                'message' => '参数错误：loginName不能为空'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 从loginName中移除merchant_code前缀（只移除开头的）
            // 先将merchant_code转为小写，然后进行判断和移除操作
            $api_user = $loginName;
            if (!empty($this->merchant_code)) {
                $merchantCodeLower = strtolower($this->merchant_code);
                $loginNameLower = strtolower($loginName);
                if (strpos($loginNameLower, $merchantCodeLower) === 0) {
                    $api_user = substr($loginName, strlen($this->merchant_code));
                }
            }
            
            // 查找用户API记录
            $userApi = User_Api::where('api_user', $api_user)->where('api_code', $this->db_code)->first();
            
            if (!$userApi) {
                Log::warning('Dbzhenren getBalance 用户不存在', [
                    'loginName' => $loginName,
                    'api_user' => $api_user,
                    'api_code' => $this->db_code
                ]);
                // 直接返回 JSON 格式的错误响应
                return json_encode([
                    'code' => 1000,
                    'message' => '会员不存在'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            
            // 从数据库中获取余额
            $balance = $userApi->api_money ?? 0;

            // 余额支持4个精度（数值类型，不是字符串）
            $balance = round($balance, 4);

            // 构建data参数（注意：balance是数值类型，不是字符串）
            $dataArray = [
                'loginName' => $loginName,
                'balance' => $balance  // 数值类型，支持4个精度
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($dataArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren getBalance 处理成功', [
                'loginName' => $loginName,
                'balance' => $balance,
                'currency' => $currency,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren getBalance 处理异常', [
                'loginName' => $loginName,
                'currency' => $currency,
                'error' => $e->getMessage()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 1000,
                'message' => '会员不存在'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 getBatchBalance 批量会员余额查询回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $getBalanceCallback 获取余额的回调函数 function($loginName, $currency) { return $balance; }
     * @return string JSON 格式字符串
     */
    public function getBatchBalance($requestData = null, $getBalanceCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren getBatchBalance 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren getBatchBalance 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren getBatchBalance params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $loginNames = $paramsData['loginNames'] ?? [];
        $currency = $paramsData['currency'] ?? 'CNY';

        if (empty($loginNames) || !is_array($loginNames)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误：loginNames不能为空'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 最多传递7个会员账号
        if (count($loginNames) > 7) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误：最多传递7个会员账号'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
        $result = [];
        foreach ($loginNames as $loginName) {
            try {
                    $balance = $getBalanceCallback ? call_user_func($getBalanceCallback, $loginName, $currency) : 0;
                // 余额支持4个精度，状态不正确或账号不存在时返回0
                $balance = round($balance, 4);
            } catch (\Exception $e) {
                // 账号不存在或状态不正确时返回0
                $balance = 0;
            }

            $result[] = [
                'loginName' => $loginName,
                'balance' => $balance
            ];
        }

            // 将data转为JSON字符串
            $dataJson = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren getBatchBalance 处理成功', [
                'loginNames_count' => count($loginNames),
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren getBatchBalance 处理异常', [
                'error' => $e->getMessage()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90000,
                'message' => '处理失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 betConfirm 下注确认回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $betConfirmCallback 下注确认回调函数
     *   function($transferNo, $loginName, $betTotalAmount, $betInfo, $gameTypeId, $roundNo, $betTime, $currency) {
     *     return ['success' => true, 'balance' => $balance, 'realBetAmount' => $realBetAmount, 'realBetInfo' => $realBetInfo];
     *   }
     * @return string JSON 格式字符串
     */
    public function betConfirm($requestData = null, $betConfirmCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren betConfirm 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren betConfirm 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren betConfirm params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $betTotalAmount = $paramsData['betTotalAmount'] ?? 0;
        $betInfo = $paramsData['betInfo'] ?? [];
        $gameTypeId = $paramsData['gameTypeId'] ?? 0;
        $roundNo = $paramsData['roundNo'] ?? '';
        $betTime = $paramsData['betTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';

        if (empty($transferNo) || empty($loginName) || empty($betInfo)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理下注确认
            $result = $betConfirmCallback ? call_user_func($betConfirmCallback, $transferNo, $loginName, $betTotalAmount, $betInfo, $gameTypeId, $roundNo, $betTime, $currency) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                // 余额不足
                return json_encode([
                    'code' => 1002,
                    'message' => $result['message'] ?? '余额不足'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'realBetAmount' => round($result['realBetAmount'], 4),
                'realBetInfo' => $result['realBetInfo']
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren betConfirm 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren betConfirm 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 betCancel 取消下注回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $betCancelCallback 取消下注回调函数
     *   function($transferNo, $loginName, $gameTypeId, $roundNo, $cancelTime, $currency, $betPayoutMap, $hasTransferOut) {
     *     return ['success' => true, 'balance' => $balance, 'rollbackAmount' => $rollbackAmount];
     *   }
     * @return string JSON 格式字符串
     */
    public function betCancel($requestData = null, $betCancelCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren betCancel 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren betCancel 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren betCancel params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $gameTypeId = $paramsData['gameTypeId'] ?? 0;
        $roundNo = $paramsData['roundNo'] ?? '';
        $cancelTime = $paramsData['cancelTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';
        $betPayoutMap = $paramsData['betPayoutMap'] ?? [];
        $hasTransferOut = $paramsData['hasTransferOut'] ?? 0;

        if (empty($transferNo) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理取消下注
            $result = $betCancelCallback ? call_user_func($betCancelCallback, $transferNo, $loginName, $gameTypeId, $roundNo, $cancelTime, $currency, $betPayoutMap, $hasTransferOut) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'rollbackAmount' => round($result['rollbackAmount'], 4)
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren betCancel 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren betCancel 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 gamePayout 派彩回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $gamePayoutCallback 派彩回调函数
     *   function($transferNo, $loginName, $payoutAmount, $gameTypeId, $roundNo, $payoutTime, $currency, $transferType, $playerId, $betPayoutMap) {
     *     return ['success' => true, 'balance' => $balance, 'realAmount' => $realAmount, 'badAmount' => $badAmount];
     *   }
     * @return string JSON 格式字符串
     */
    public function gamePayout($requestData = null, $gamePayoutCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren gamePayout 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren gamePayout 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren gamePayout params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $payoutAmount = $paramsData['payoutAmount'] ?? 0;
        $gameTypeId = $paramsData['gameTypeId'] ?? 0;
        $roundNo = $paramsData['roundNo'] ?? '';
        $payoutTime = $paramsData['payoutTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';
        $transferType = $paramsData['transferType'] ?? 'PAYOUT';
        $playerId = $paramsData['playerId'] ?? 0;
        $betPayoutMap = $paramsData['betPayoutMap'] ?? [];

        if (empty($transferNo) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理派彩
            $result = $gamePayoutCallback ? call_user_func($gamePayoutCallback, $transferNo, $loginName, $payoutAmount, $gameTypeId, $roundNo, $payoutTime, $currency, $transferType, $playerId, $betPayoutMap) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'realAmount' => round($result['realAmount'], 6),
                'badAmount' => round($result['badAmount'], 6)
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren gamePayout 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren gamePayout 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 activityPayout 活动和小费类回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $activityPayoutCallback 活动派彩回调函数
     *   function($transferNo, $loginName, $payoutAmount, $payoutType, $transferType, $playerId, $payoutTime, $currency, $hasTransferOut) {
     *     return ['success' => true, 'balance' => $balance, 'realAmount' => $realAmount, 'badAmount' => $badAmount];
     *   }
     * @return string JSON 格式字符串
     */
    public function activityPayout($requestData = null, $activityPayoutCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren activityPayout 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren activityPayout 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren activityPayout params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $payoutAmount = $paramsData['payoutAmount'] ?? 0;
        $payoutType = $paramsData['payoutType'] ?? '';
        $transferType = $paramsData['transferType'] ?? '';
        $playerId = $paramsData['playerId'] ?? 0;
        $payoutTime = $paramsData['payoutTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';
        $hasTransferOut = $paramsData['hasTransferOut'] ?? 0;

        if (empty($transferNo) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理活动派彩
            $result = $activityPayoutCallback ? call_user_func($activityPayoutCallback, $transferNo, $loginName, $payoutAmount, $payoutType, $transferType, $playerId, $payoutTime, $currency, $hasTransferOut) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                // 活动和消费类不允许产生坏账，余额不足时返回失败
                if ($payoutType === 'DEDUCTION' && $result['code'] == 1002) {
                    return json_encode([
                        'code' => 1002,
                        'message' => '余额不足'
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                return json_encode([
                    'code' => $result['code'] ?? 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'realAmount' => round($result['realAmount'], 4),
                'badAmount' => round($result['badAmount'], 4)
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren activityPayout 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren activityPayout 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 playerbetting 玩家下注推送回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $playerbettingCallback 下注推送回调函数
     *   function($changePayout, $bettingRecordList) {
     *     return ['success' => true];
     *   }
     * @return string JSON 格式字符串
     */
    public function playerBetting($requestData = null, $playerbettingCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren playerBetting 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren playerBetting 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren playerBetting params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        if (empty($paramsData) || !is_array($paramsData)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理下注推送
            $result = $playerbettingCallback ? call_user_func($playerbettingCallback, $paramsData) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'merchantCode' => $this->merchant_code
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren playerBetting 处理成功', [
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren playerBetting 处理异常', [
                'error' => $e->getMessage(),
                'params_data' => $paramsData
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 activityRebate 返利活动推送回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $activityRebateCallback 返利推送回调函数
     *   function($detailId, $activityType, $agentId, $agentCode, $playerId, $loginName, $activityId, $activityName, $createdTime, $rewardAmount) {
     *     return ['success' => true];
     *   }
     * @return string JSON 格式字符串
     */
    public function activityRebate($requestData = null, $activityRebateCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren activityRebate 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren activityRebate 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren activityRebate params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $detailId = $paramsData['detailId'] ?? 0;
        $activityType = $paramsData['activityType'] ?? 0;
        $agentId = $paramsData['agentId'] ?? 0;
        $agentCode = $paramsData['agentCode'] ?? '';
        $playerId = $paramsData['playerId'] ?? 0;
        $loginName = $paramsData['loginName'] ?? '';
        $activityId = $paramsData['activityId'] ?? 0;
        $activityName = $paramsData['activityName'] ?? '';
        $createdTime = $paramsData['createdTime'] ?? '';
        $rewardAmount = $paramsData['rewardAmount'] ?? 0;

        if (empty($detailId) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理返利推送
            $result = $activityRebateCallback ? call_user_func($activityRebateCallback, $detailId, $activityType, $agentId, $agentCode, $playerId, $loginName, $activityId, $activityName, $createdTime, $rewardAmount) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'merchantCode' => $this->merchant_code
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren activityRebate 处理成功', [
                'detailId' => $detailId,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren activityRebate 处理异常', [
                'detailId' => $detailId,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 解析回调请求参数
     * 
     * @param array $request 原始请求数据
     * @return array ['merchantCode', 'transferNo', 'params', 'signature', 'timestamp', 'paramsArray']
     */
    public function parseRequest($request)
    {
        $merchantCode = $request['merchantCode'] ?? '';
        $transferNo = $request['transferNo'] ?? '';
        $params = $request['params'] ?? '';
        $signature = $request['signature'] ?? '';
        $timestamp = $request['timestamp'] ?? 0;

        // 解析params JSON字符串
        $paramsArray = [];
        if (!empty($params)) {
            $paramsArray = json_decode($params, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Dbzhenren 解析params JSON失败', [
                    'params' => $params,
                    'json_error' => json_last_error_msg()
                ]);
            }
        }

        return [
            'merchantCode' => $merchantCode,
            'transferNo' => $transferNo,
            'params' => $params,
            'signature' => $signature,
            'timestamp' => $timestamp,
            'paramsArray' => $paramsArray
        ];
    }

    /**
     * 验证回调请求（包括签名验证）
     * 
     * @param array $request 原始请求数据
     * @return array ['valid' => bool, 'parsed' => array, 'error' => string]
     */
    public function validateRequest($request)
    {
        $parsed = $this->parseRequest($request);

        // 验证商户号
        if ($parsed['merchantCode'] !== $this->merchant_code) {
            return [
                'valid' => false,
                'parsed' => $parsed,
                'error' => '商户号不匹配'
            ];
        }

        // 验证签名
        if (!$this->verifySign($parsed['params'], $parsed['signature'])) {
            return [
                'valid' => false,
                'parsed' => $parsed,
                'error' => '签名验证失败'
            ];
        }

        return [
            'valid' => true,
            'parsed' => $parsed,
            'error' => ''
        ];
    }

    /**
     * 生成API请求签名（用于数据接口）
     * 签名算法：MD5("业务原文JSON+MD5盐值")
     * 
     * @param string $source 业务参数JSON字符串
     * @return string MD5签名（大写）
     */
    private function generateApiSign($source)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbzhenren 密钥未配置');
            return '';
        }
        return strtoupper(md5($source . $this->secret_key));
    }

    /**
     * 构建加密请求参数（根据文档3.2节）
     * 将业务参数进行AES加密和MD5签名
     * 
     * @param array $businessParams 业务参数（原始JSON参数）
     * @return array 包含merchantCode、params（加密）、signature（签名）的请求参数
     */
    private function buildEncryptedRequest($businessParams)
    {
        // 在公共方法中统一设置 lang 固定为1
        $businessParams['lang'] = 1;
        
        // 记录原始业务参数
        Log::info('Dbzhenren 开始组装加密请求参数', [
            'business_params' => $businessParams,
            'merchant_code' => $this->merchant_code
        ]);
        
        // 1. 将业务参数转为JSON字符串
        // 注意：JavaScript 的 JSON.stringify 会保持数字类型为数字，字符串为字符串
        // 但参考代码中有些字段是字符串类型（如 deviceType: "1"），需要确保类型一致
        $sourceJson = json_encode($businessParams, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($sourceJson === false) {
            Log::error('Dbzhenren 业务参数JSON编码失败', [
                'params' => $businessParams,
                'error' => json_last_error_msg()
            ]);
            return null;
        }
        
        // 记录JSON字符串（用于调试对比）
        Log::info('Dbzhenren 业务参数JSON字符串', [
            'source_json' => $sourceJson,
            'json_length' => strlen($sourceJson),
            'business_params' => $businessParams
        ]);
        
        // 2. 生成MD5签名：MD5(原始JSON + md5Key)
        // 参考代码：var signature = CryptoJS.MD5(params + md5Kye).toString().toUpperCase();
        $signString = $sourceJson . $this->secret_key;
        $signature = strtoupper(md5($signString));
        
        Log::info('Dbzhenren 生成MD5签名', [
            'source_json' => $sourceJson,
            'secret_key' => $this->secret_key,
            'sign_string' => $signString, // 完整签名字符串，用于对比
            'sign_string_length' => strlen($signString),
            'signature' => $signature
        ]);
        
        // 3. AES加密：AES/ECB/PKCS5Padding
        $encryptedParams = $this->aesEncrypt($sourceJson, $this->aes_key);
        
        if (empty($encryptedParams)) {
            Log::error('Dbzhenren AES加密失败', [
                'source_json_length' => strlen($sourceJson),
                'aes_key_length' => strlen($this->aes_key)
            ]);
            return null;
        }
        
        Log::info('Dbzhenren AES加密完成', [
            'encrypted_params_length' => strlen($encryptedParams),
            'encrypted_params_preview' => substr($encryptedParams, 0, 50) . '...'
        ]);
        
        // 4. 构建最终请求参数（只包含三个字段：merchantCode, params, signature）
        $finalParams = [
            'merchantCode' => $this->merchant_code,
            'params' => $encryptedParams, // AES加密后的Base64字符串
            'signature' => $signature      // MD5签名（大写）
        ];
        
        // 记录最终组装的参数
        Log::info('Dbzhenren 请求参数组装完成', [
            'merchant_code' => $this->merchant_code,
            'params_length' => strlen($encryptedParams),
            'signature' => $signature,
            'final_params_keys' => array_keys($finalParams), // 确认只有三个字段
            'final_params' => [
                'merchantCode' => $finalParams['merchantCode'],
                'params' => substr($encryptedParams, 0, 50) . '... (AES加密后的Base64)',
                'signature' => $finalParams['signature']
            ],
            'note' => '最终请求参数只包含三个字段：merchantCode, params, signature'
        ]);
        
        return $finalParams;
    }

    /**
     * 发送HTTP请求
     * 根据文档3.1节，所有接口都使用POST方式，传送JSON形式的数据
     *
     * @param string $url API地址
     * @param array $params 业务参数（会被加密和签名）
     * @param string $method 请求方法（POST/GET，默认POST）
     * @param string $contentType Content-Type（application/json 或 application/x-www-form-urlencoded，默认application/json）
     * @param array $headers 额外的请求头
     * @param bool $needEncrypt 是否需要加密（默认true，根据文档所有接口都需要加密）
     * @return array
     */
    private function sendRequest($url, $params = [], $method = 'POST', $contentType = 'application/json', $headers = [], $needEncrypt = true)
    {
        // 请求前日志记录
        Log::info('Dbzhenren 请求开始', [
            'url' => $url,
            'method' => $method,
            'content_type' => $contentType,
            'need_encrypt' => $needEncrypt,
            'original_params' => $params,
            'headers' => $headers,
            'params_count' => count($params),
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $startTime = microtime(true); // 记录请求开始时间
        
        $ch = curl_init();
        $requestUrl = $url;
        $requestBody = '';
        $businessParams = $params; // 保存原始业务参数用于日志
        $finalRequestParams = null; // 保存最终请求参数（merchantCode, params, signature）

        if ($method === 'GET') {
            // GET请求不加密（如 /api/merchant/ok）
            $queryString = http_build_query($params);
            $requestUrl = $url . '?' . $queryString;
            
            // 记录GET请求参数
            Log::info('Dbzhenren GET请求参数组装完成', [
                'url' => $url,
                'request_url' => $requestUrl,
                'params' => $params,
                'query_string' => $queryString
            ]);
            
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_POST, false);
            $requestBody = $queryString;
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            
            // 根据文档3.2节，所有接口都需要加密和签名（除了GET请求）
            if ($needEncrypt && $contentType === 'application/json') {
                // 构建加密请求参数
                $encryptedParams = $this->buildEncryptedRequest($params);
                
                if ($encryptedParams === null) {
                    Log::error('Dbzhenren 请求参数加密失败，终止请求', [
                        'url' => $url,
                        'business_params' => $params
                    ]);
                    curl_close($ch);
                    return [
                        'code' => -1,
                        'message' => '请求参数加密失败'
                    ];
                }
                
                // 保存最终请求参数（merchantCode, params, signature）
                $finalRequestParams = $encryptedParams;
                
                // 将加密后的参数转为JSON
                $requestBody = json_encode($encryptedParams, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                
                if ($requestBody === false) {
                    $jsonError = json_last_error_msg();
                    Log::error('Dbzhenren 加密参数JSON编码失败', [
                        'url' => $url,
                        'encrypted_params' => $encryptedParams,
                        'error' => $jsonError
                    ]);
                    curl_close($ch);
                    return [
                        'code' => -1,
                        'message' => 'JSON编码失败：' . $jsonError
                    ];
                }
                
                // 确保JSON字符串是UTF-8编码，去除BOM
                $requestBody = preg_replace('/^\xEF\xBB\xBF/', '', $requestBody);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                $requestHeaders = array_merge([
                    'Content-Type: application/json'
                ], $headers);
            } elseif ($contentType === 'application/json') {
                // 不需要加密的JSON请求（特殊情况）
                $finalRequestParams = $params; // 保存最终请求参数
                $requestBody = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                
                if ($requestBody === false) {
                    $jsonError = json_last_error_msg();
                    Log::error('Dbzhenren JSON编码失败', [
                        'url' => $url,
                        'params' => $params,
                        'error' => $jsonError
                    ]);
                    curl_close($ch);
                    return [
                        'code' => -1,
                        'message' => 'JSON编码失败：' . $jsonError
                    ];
                }
                
                $requestBody = preg_replace('/^\xEF\xBB\xBF/', '', $requestBody);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                $requestHeaders = array_merge([
                    'Content-Type: application/json'
                ], $headers);
            } else {
                // 表单格式（不常用，但保留兼容性）
                $finalRequestParams = $params; // 保存最终请求参数
                $requestBody = http_build_query($params);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                $requestHeaders = array_merge([
                    'Content-Type: application/x-www-form-urlencoded'
                ], $headers);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // 根据文档3.5节，超时时间设置为30秒
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 记录最终请求参数和原始字符串
        // 对于加密请求，finalRequestParams 应该是 merchantCode, params, signature 三个字段
        if ($method === 'GET') {
            $finalRequestParams = $params;
        }
        
        Log::info('Dbzhenren 最终请求参数和原始字符串', [
            'url' => $requestUrl,
            'method' => $method,
            'content_type' => $contentType,
            'final_request_params' => $finalRequestParams, // 最终请求的参数数组（加密请求应该是 merchantCode, params, signature）
            'final_request_params_keys' => $finalRequestParams ? array_keys($finalRequestParams) : [], // 确认字段名称
            'final_request_params_count' => $finalRequestParams ? count($finalRequestParams) : 0, // 确认字段数量
            'request_body_raw_string' => $requestBody, // 请求的原始JSON字符串（完整内容）
            'request_body_length' => strlen($requestBody),
            'business_params' => $businessParams, // 原始业务参数（用于对比）
            'need_encrypt' => $needEncrypt,
            'note' => $needEncrypt && $contentType === 'application/json' ? '加密请求：最终参数应该是 merchantCode, params, signature 三个字段' : ''
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $endTime = microtime(true); // 记录请求结束时间
        $duration = round(($endTime - $startTime) * 1000, 2); // 计算请求耗时（毫秒）

        if ($curlError) {
            Log::error('Dbzhenren API请求CURL错误', [
                'url' => $requestUrl,
                'method' => $method,
                'curl_error' => $curlError,
                'http_code' => $httpCode,
                'duration_ms' => $duration
            ]);
            
            // 请求后日志记录（CURL错误）
            Log::info('Dbzhenren 请求结束（CURL错误）', [
                'url' => $requestUrl,
                'method' => $method,
                'http_code' => $httpCode,
                'duration_ms' => $duration,
                'status' => 'failed',
                'error' => $curlError,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'code' => -1,
                'message' => '请求失败：' . $curlError
            ];
        }

        // 记录响应日志
        $responseData = json_decode($response, true);
        Log::info('Dbzhenren API响应', [
            'url' => $requestUrl,
            'method' => $method,
            'http_code' => $httpCode,
            'response' => $responseData ?: $response,
            'response_length' => strlen($response),
            'duration_ms' => $duration
        ]);

        // 如果HTTP状态码不是200，统一返回错误格式
        if ($httpCode !== 200) {
            $errorMessage = '请求失败';
            if ($responseData && is_array($responseData)) {
                // 尝试从响应中提取错误信息
                $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'HTTP ' . $httpCode;
            } else {
                $errorMessage = 'HTTP ' . $httpCode . ($response ? ': ' . substr($response, 0, 200) : '');
            }
            
            Log::error('Dbzhenren API请求失败', [
                'url' => $requestUrl,
                'http_code' => $httpCode,
                'response' => $responseData ?: $response,
                'duration_ms' => $duration
            ]);
            
            // 请求后日志记录（HTTP错误）
            Log::info('Dbzhenren 请求结束（HTTP错误）', [
                'url' => $requestUrl,
                'method' => $method,
                'http_code' => $httpCode,
                'duration_ms' => $duration,
                'status' => 'failed',
                'error' => $errorMessage,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'code' => $httpCode,
                'message' => $errorMessage,
                'data' => $responseData ?: null
            ];
        }

        if (!$responseData || !is_array($responseData)) {
            Log::error('Dbzhenren API响应解析失败', [
                'url' => $requestUrl,
                'http_code' => $httpCode,
                'response' => $response,
                'duration_ms' => $duration
            ]);
            
            // 请求后日志记录（解析失败）
            Log::info('Dbzhenren 请求结束（解析失败）', [
                'url' => $requestUrl,
                'method' => $method,
                'http_code' => $httpCode,
                'duration_ms' => $duration,
                'status' => 'failed',
                'error' => '响应解析失败',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'code' => -1,
                'message' => '响应解析失败',
                'data' => null,
                'raw_response' => $response
            ];
        }

        // 如果响应中没有code字段，尝试从HTTP状态码或其他字段推断
        if (!isset($responseData['code'])) {
            // 检查是否有status字段（Spring Boot错误格式）
            if (isset($responseData['status'])) {
                $responseData['code'] = $responseData['status'];
            } else {
                // 默认设置为200（因为HTTP状态码已经是200了）
                $responseData['code'] = 200;
            }
        }

        // 请求后日志记录（成功）
        $status = ($responseData['code'] == 200 || $responseData['code'] == '200') ? 'success' : 'failed';
        Log::info('Dbzhenren 请求结束', [
            'url' => $requestUrl,
            'method' => $method,
            'http_code' => $httpCode,
            'response_code' => $responseData['code'] ?? 'unknown',
            'duration_ms' => $duration,
            'status' => $status,
            'message' => $responseData['message'] ?? '',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        return $responseData;
    }

    /**
     * AES加密（AES/ECB/PKCS5Padding）
     * 参考 CryptoJS.AES.encrypt 的实现
     * 
     * @param string $data 要加密的数据
     * @param string $key 加密密钥（UTF-8字符串）
     * @return string Base64编码的加密结果
     */
    private function aesEncrypt($data, $key)
    {
        if (empty($key)) {
            Log::error('Dbzhenren AES密钥未配置');
            return '';
        }
        
        // CryptoJS 使用 UTF-8 解析密钥，然后根据密钥长度选择 AES-128 或 AES-256
        // 如果密钥长度 <= 16 字节，使用 AES-128；如果 <= 32 字节，使用 AES-256
        $keyBytes = mb_convert_encoding($key, 'UTF-8', 'UTF-8');
        $keyLength = strlen($keyBytes);
        
        // 根据密钥长度选择加密算法
        if ($keyLength <= 16) {
            // AES-128：密钥长度必须是 16 字节，不足则用 null 填充
            $paddedKey = str_pad($keyBytes, 16, "\0", STR_PAD_RIGHT);
            $cipher = 'AES-128-ECB';
        } elseif ($keyLength <= 24) {
            // AES-192：密钥长度必须是 24 字节
            $paddedKey = str_pad($keyBytes, 24, "\0", STR_PAD_RIGHT);
            $cipher = 'AES-192-ECB';
        } else {
            // AES-256：密钥长度必须是 32 字节，超过则截断
            $paddedKey = substr(str_pad($keyBytes, 32, "\0", STR_PAD_RIGHT), 0, 32);
            $cipher = 'AES-256-ECB';
        }
        
        Log::info('Dbzhenren AES加密参数', [
            'key_length' => $keyLength,
            'padded_key_length' => strlen($paddedKey),
            'cipher' => $cipher,
            'data_length' => strlen($data)
        ]);
        
        // PHP的openssl_encrypt默认使用PKCS7填充（等同于PKCS5）
        $encrypted = openssl_encrypt($data, $cipher, $paddedKey, OPENSSL_RAW_DATA);
        
        if ($encrypted === false) {
            $error = openssl_error_string();
            Log::error('Dbzhenren AES加密失败', [
                'error' => $error,
                'cipher' => $cipher,
                'key_length' => strlen($paddedKey),
                'data_length' => strlen($data)
            ]);
            return '';
        }
        
        // CryptoJS 返回的是 Base64 编码的字符串
        $base64Result = base64_encode($encrypted);
        
        Log::info('Dbzhenren AES加密成功', [
            'encrypted_length' => strlen($encrypted),
            'base64_length' => strlen($base64Result),
            'base64_preview' => substr($base64Result, 0, 50) . '...'
        ]);
        
        return $base64Result;
    }

    /**
     * AES解密（AES/ECB/PKCS5Padding）
     * 
     * @param string $encryptedData Base64编码的加密数据
     * @param string $key 解密密钥
     * @return string 解密后的原始数据
     */
    private function aesDecrypt($encryptedData, $key)
    {
        if (empty($key)) {
            Log::error('Dbzhenren AES密钥未配置');
            return '';
        }
        
        $data = base64_decode($encryptedData);
        $decrypted = openssl_decrypt($data, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        
        if ($decrypted === false) {
            Log::error('Dbzhenren AES解密失败', [
                'error' => openssl_error_string()
            ]);
            return '';
        }
        
        return $decrypted;
    }

    /**
     * 检测是否为移动端访问
     * 
     * @return bool
     */
    private function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        return false;
    }

    /**
     * 根据接口路径判断接口类型并返回正确的API URL
     * 
     * @param string $path 接口路径（如 /api/merchant/create/v2 或 /data/merchant/betHistoryRecord/v1）
     * @return string 完整的API URL
     */
    private function getApiUrl($path)
    {
        // 判断是基础接口还是数据接口
        if (strpos($path, '/data/merchant/') === 0) {
            // 数据接口：使用 api_data_url（如果配置了），否则使用 api_url
            $baseUrl = !empty($this->api_data_url) ? $this->api_data_url : $this->api_url;
        } else {
            // 基础接口：使用 api_url
            $baseUrl = $this->api_url;
        }
        
        return rtrim($baseUrl, '/') . $path;
    }

    /**
     * 发送数据接口请求（带签名和Header）
     * 用于5.x节的数据接口
     * 根据文档3.2节，数据接口也需要加密和签名
     *
     * @param string $method 接口方法名（如 betHistoryRecord, reportPlayer）
     * @param array $params 业务参数
     * @param int $pageIndex 页码（用于Header）
     * @return array
     */
    private function sendDataRequest($method, $params, $pageIndex = 1)
    {
        if (empty($this->api_url)) {
            Log::error('Dbzhenren API URL未配置');
            return [
                'code' => -1,
                'message' => 'API URL未配置'
            ];
        }

        // 生成时间戳（如果不存在）
        if (!isset($params['timestamp'])) {
            $params['timestamp'] = time() * 1000; // 毫秒级时间戳
        }

        // 设置请求头（数据接口的特殊headers）
        $headers = [
            'merchantCode: ' . $this->merchant_code,
            'pageIndex: ' . $pageIndex
        ];

        $url = $this->getApiUrl('/data/merchant/' . $method . '/v1');
        
        // 使用sendRequest方法，它会自动对业务参数进行加密和签名
        return $this->sendRequest($url, $params, 'POST', 'application/json', $headers, true);
    }

    /**
     * 4.1 创建游戏账号
     * API地址：/api/merchant/create/v1 或 /api/merchant/create/v2
     * 
     * @param string $loginName 游戏账号（6-50字符，需要包括商户的前缀，只能包含以下特殊字符[下划线、@、#、&、*]）
     * @param string $loginPassword 登陆密码（6-32字符，不允许的符号：`''[]./'"'$）
     * @param string $api_code API代码（可选）
     * @param string $nickName 昵称（可选，最多12位的数字+字母，以及允许下划线和@符号）
     * @param int $oddType 盘口类型（V1版本必填，V2版本已取消，请参考限红类型附件）
     * @param int $lang 语言（固定设为1）
     * @param string $version 版本（v1 或 v2，默认v2，建议使用V2版本）
     * @return array 返回格式：['code' => 200, 'message' => '成功', 'request' => [...], 'data' => [...]]
     */
    public function register($loginName, $loginPassword, $api_code = "", $nickName = '', $oddType = 0, $lang = 1, $version = 'v2')
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        // V1版本必须要有oddType参数
        if ($version === 'v1' && $oddType <= 0) {
            return [
                'code' => 400,
                'message' => 'V1版本必须提供oddType参数（盘口类型）'
            ];
        }

        // 在用户名前面加上merchant_code前缀（如果还没有的话）
        $fullLoginName = (string)$loginName;
        if (!empty($this->merchant_code)) {
            // 检查用户名是否已经包含merchant_code前缀
            if (strpos($fullLoginName, $this->merchant_code) !== 0) {
                $fullLoginName = $this->merchant_code . $fullLoginName;
            }
        }

        // 参数验证（在加上前缀后验证）
        if (empty($fullLoginName) || strlen($fullLoginName) < 6 || strlen($fullLoginName) > 50) {
            return [
                'code' => 400,
                'message' => '游戏账号长度必须在6-50字符之间（包含商户前缀）'
            ];
        }

        if (empty($loginPassword) || strlen($loginPassword) < 6 || strlen($loginPassword) > 32) {
            return [
                'code' => 400,
                'message' => '登陆密码长度必须在6-32字符之间'
            ];
        }

        if (!empty($nickName) && strlen($nickName) > 12) {
            return [
                'code' => 400,
                'message' => '昵称最多12位'
            ];
        }

        // 确保参数类型正确
        $params = [
            'loginName' => $fullLoginName,
            'loginPassword' => (string)$loginPassword,
            'timestamp' => (int)(time() * 1000), // 确保是整数类型
        ];

        // 昵称可选参数
        if (!empty($nickName)) {
            $params['nickName'] = (string)$nickName;
        }

        // V1版本必须包含oddType参数
        if ($version === 'v1') {
            $params['oddType'] = (int)$oddType;
        }

        $url = $this->getApiUrl('/api/merchant/create/' . $version);
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.2 快捷开始游戏(三合一)
     * 综合了创建游戏账号接口、上分接口、进入游戏接口
     * 
     * @param string $loginName 游戏账号
     * @param string $loginPassword 游戏密码
     * @param int $deviceType 设备类型（1=PC, 2=H5, 3=iOS, 4=Android）
     * @param float $amount 转账金额（可选，大于0时视为期望同时带入余额）
     * @param string $transferNo 转账单号（可选，带金额时必填）
     * @param int $oddType 盘口类型（V1版本必填，V2版本已取消）
     * @param int $lang 语言（固定设为1）
     * @param string $backurl 返回商户地址（可选）
     * @param string $domain 动态游戏域名（可选）
     * @param int $showExit 是否显示退出按钮（0=显示，1=不显示）
     * @param int $gameTypeId 游戏类型ID（可选）
     * @param int $anchorId 主播ID（可选）
     * @param string $ip 透传玩家真实ip（可选）
     * @param int $isCompetition 是否进入大赛（0=大厅，1=大赛）
     * @param string $playerLanguageV2 玩家首次登录默认语言（可选）
     * @param string $version 版本（v1 或 v2，默认v2）
     * @return array
     */
    public function fastGame($loginName, $loginPassword, $deviceType = 2, $amount = 0, $transferNo = '', $oddType = 0, $lang = 1, $backurl = '', $domain = '', $showExit = 0, $gameTypeId = 0, $anchorId = 0, $ip = '', $isCompetition = 0, $playerLanguageV2 = '', $version = 'v2')
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        // 在用户名前面加上merchant_code前缀（如果还没有的话）
        $fullLoginName = (string)$loginName;
        if (!empty($this->merchant_code)) {
            // 检查用户名是否已经包含merchant_code前缀
            if (strpos($fullLoginName, $this->merchant_code) !== 0) {
                $fullLoginName = $this->merchant_code . $fullLoginName;
            }
        }

        $params = [
            'loginName' => $fullLoginName,
            'loginPassword' => $loginPassword,
            'deviceType' => $deviceType,
            'timestamp' => time() * 1000,
        ];

        // V1版本需要oddType参数
        if ($version === 'v1' && $oddType > 0) {
            $params['oddType'] = $oddType;
        }

        if (!empty($backurl)) {
            $params['backurl'] = $backurl;
        }
        if (!empty($domain)) {
            $params['domain'] = $domain;
        }
        if ($showExit > 0) {
            $params['showExit'] = $showExit;
        }
        if ($gameTypeId > 0) {
            $params['gameTypeId'] = $gameTypeId;
        }
        if ($anchorId > 0) {
            $params['anchorId'] = $anchorId;
        }
        if (!empty($ip)) {
            $params['ip'] = $ip;
        }
        if ($isCompetition > 0) {
            $params['isCompetition'] = $isCompetition;
        }
        if (!empty($playerLanguageV2)) {
            $params['playerLanguageV2'] = $playerLanguageV2;
        }

        // 如果传递了金额，则同时上分
        if ($amount > 0) {
            $params['amount'] = $amount;
            if (empty($transferNo)) {
                $transferNo = time() . rand(100000, 999999);
            }
            $params['transferNo'] = $transferNo;
        }

        $url = $this->getApiUrl('/api/merchant/fastGame/' . $version);
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.7 禁止/开启投注状态
     * 
     * @param string $loginName 游戏账号
     * @param int $enabled 状态（0=开启，1=禁用）
     * @return array
     */
    public function enableUserBet($loginName, $enabled = 1)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'enabled' => $enabled,
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/enableUserBetd/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.8 重置游戏登陆密码
     * 
     * @param string $loginName 游戏账号
     * @param string $newPassword 新的密码
     * @return array
     */
    public function resetPassword($loginName, $newPassword)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'newPassword' => $newPassword,
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/resetLoginPwd/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.9 进入游戏
     * 
     * @param string $loginName 游戏账号
     * @param string $loginPassword 游戏密码
     * @param string $api_code API代码（可选）
     * @param int $deviceType 设备类型（1=PC, 2=H5, 3=iOS, 4=Android）
     * @param int $oddType 盘口类型（V1版本必填，V2版本已取消）
     * @param int $lang 语言（固定设为1）
     * @param string $backurl 返回商户地址（可选）
     * @param string $domain 动态游戏域名（可选）
     * @param int $showExit 是否显示退出按钮（0=显示，1=不显示）
     * @param int $gameTypeId 游戏类型ID（可选）
     * @param int $anchorId 主播ID（可选）
     * @param string $ip 透传玩家真实ip（可选）
     * @param int $isCompetition 是否进入大赛（0=大厅，1=大赛）
     * @param string $playerLanguageV2 玩家首次登录默认语言（可选）
     * @param string $version 版本（v1 或 v2，默认v2）
     * @return array
     */
    public function login($loginName, $loginPassword, $api_code = "", $deviceType = 2, $oddType = 0, $lang = 1, $backurl = '', $domain = '', $showExit = 0, $gameTypeId = "", $anchorId = 0, $ip = '', $isCompetition = 0, $playerLanguageV2 = '', $version = 'v2')
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        // 在用户名前面加上merchant_code前缀（如果还没有的话）
        $fullLoginName = (string)$loginName;
        if (!empty($this->merchant_code)) {
            // 检查用户名是否已经包含merchant_code前缀
            if (strpos($fullLoginName, $this->merchant_code) !== 0) {
                $fullLoginName = $this->merchant_code . $fullLoginName;
            }
        }

        // 如果没有传入 backurl，根据访问类型自动设置
        if (empty($backurl)) {
            $isMobile = $this->isMobile();
            if ($isMobile) {
                $backurl = env('WAP_URL', '');
            } else {
                $backurl = env('PC_URL', '');
            }
        }

        $params = [
            'loginName' => $fullLoginName,
            'loginPassword' => $loginPassword,
            'deviceType' => $deviceType,
            'timestamp' => time() * 1000,
        ];

        // V1版本需要oddType参数
        if ($version === 'v1' && $oddType > 0) {
            $params['oddType'] = $oddType;
        }

        if (!empty($backurl)) {
            $params['backurl'] = $backurl;
        }
        if (!empty($domain)) {
            $params['domain'] = $domain;
        }
        if ($showExit > 0) {
            $params['showExit'] = $showExit;
        }
        if ($gameTypeId > 0) {
            $params['gameTypeId'] = $gameTypeId;
        }
        if ($anchorId > 0) {
            $params['anchorId'] = $anchorId;
        }
        if (!empty($ip)) {
            $params['ip'] = $ip;
        }
        if ($isCompetition > 0) {
            $params['isCompetition'] = $isCompetition;
        }
        if (!empty($playerLanguageV2)) {
            $params['playerLanguageV2'] = $playerLanguageV2;
        }

        $url = $this->getApiUrl('/api/merchant/forwardGame/' . $version);
        $res = $this->sendRequest($url, $params, 'POST');
        $res["data"] = $res["code"] == 200 ? $res["data"]["url"] : "";
        return $res;
    }

    /**
     * 4.11 获取游戏维护状态
     * 
     * @return array
     */
    public function checkMaintenance()
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/checkMaintaince/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.12 校验API接口是否可访问
     * GET方法，无参数
     * 
     * @return array
     */
    public function checkOk()
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $url = $this->getApiUrl('/api/merchant/ok');
        return $this->sendRequest($url, [], 'GET');
    }

    /**
     * 4.14 会员离桌接口
     * 
     * @param string $loginName 游戏账号
     * @param int $tableId 桌台id（可选）
     * @return array
     */
    public function leaveTable($loginName, $tableId = 0)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'timestamp' => time() * 1000,
        ];

        if ($tableId > 0) {
            $params['tableId'] = $tableId;
        }

        $url = $this->getApiUrl('/api/merchant/foreLeaveTable/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.15 获取各商户场馆桌台数量
     * 
     * @return array
     */
    public function getTableNumber()
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/agentTableNumber/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 5.2 游戏记录(时间区间)
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @return array
     */
    public function getGameRecords($startTime, $endTime, $pageIndex = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('betHistoryRecord', $params, $pageIndex);
    }

    /**
     * 5.3 对账接口（按代理按日统计注单量）
     * 
     * @param int $startDate 报表开始日期（格式：yyyyMMdd）
     * @param int $endDate 报表结束日期（格式：yyyyMMdd）
     * @param int $pageIndex 页码
     * @param int $exchange 是否转换为商户货币（0=游戏币，1=商户货币）
     * @return array
     */
    public function getReportAgent($startDate, $endDate, $pageIndex = 1, $exchange = 0)
    {
        $params = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($exchange > 0) {
            $params['exchange'] = $exchange;
        }

        return $this->sendDataRequest('reportAgent', $params, $pageIndex);
    }

    /**
     * 5.4 对账接口（按会员按日统计注单量）
     * 
     * @param int $startDate 报表开始日期（格式：yyyyMMdd）
     * @param int $endDate 报表结束日期（格式：yyyyMMdd）
     * @param int $pageIndex 页码
     * @param int $exchange 是否转换为商户货币（0=游戏币，1=商户货币）
     * @return array
     */
    public function getReportPlayer($startDate, $endDate, $pageIndex = 1, $exchange = 0)
    {
        $params = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($exchange > 0) {
            $params['exchange'] = $exchange;
        }

        return $this->sendDataRequest('reportPlayer', $params, $pageIndex);
    }

    /**
     * 5.5 查询在线会员列表
     * 
     * @param int $pageIndex 页码
     * @return array
     */
    public function getOnlineUsers($pageIndex = 1)
    {
        $params = [
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('onlineUsers', $params, $pageIndex);
    }

    /**
     * 5.6 活动彩金数据
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @param int $activityType 活动类型（可选，1=红包雨，2=玩法返利，10=任务奖励，11=抽奖，12=兑奖）
     * @return array
     */
    public function getActivityRecord($startTime, $endTime, $pageIndex = 1, $activityType = 0)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($activityType > 0) {
            $params['activityType'] = $activityType;
        }

        return $this->sendDataRequest('activityRecord', $params, $pageIndex);
    }

    /**
     * 5.7 打赏明细数据
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @return array
     */
    public function getRewardRecord($startTime, $endTime, $pageIndex = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('rewardRecordList', $params, $pageIndex);
    }

    /**
     * 5.8 大赛流水记录(时间区间)
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @return array
     */
    public function getMatchAccountChange($startTime, $endTime, $pageIndex = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('matchAccountChange', $params, $pageIndex);
    }

    /**
     * 5.9 主播列表
     * 
     * @param int $clientType 客户端类型（1=WEB, 2=iOS, 3=Android）
     * @param string $ip 客户端ip
     * @param int $pageIndex 页码
     * @param int $pageSize 每页条数
     * @return array
     */
    public function getLives($clientType = 1, $ip = '', $pageIndex = 1, $pageSize = 10)
    {
        $params = [
            'clientType' => $clientType,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
            'timestamp' => time() * 1000,
        ];

        if (!empty($ip)) {
            $params['ip'] = $ip;
        }

        return $this->sendDataRequest('lives', $params, $pageIndex);
    }

    /**
     * 5.10 异常注单状态查询
     * 
     * @param int $id 注单号（可选）
     * @param string $roundNo 局号（可选）
     * @param array $roundNoList 注单局号集合（可选，批量查询，数量不能大于50）
     * @param int $dataStatus 注单状态（可选，1=正常，2=商户禁用，3=下注失败，4=余额不足，5=局状态不对，6=下注确认失败，7=其它异常）
     * @param int $pageIndex 页码
     * @return array
     */
    public function queryAbnormalBetting($id = 0, $roundNo = '', $roundNoList = [], $dataStatus = 0, $pageIndex = 1)
    {
        $params = [
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($id > 0) {
            $params['id'] = $id;
        }
        if (!empty($roundNo)) {
            $params['roundNo'] = $roundNo;
        }
        if (!empty($roundNoList) && is_array($roundNoList) && count($roundNoList) <= 50) {
            $params['roundNoList'] = $roundNoList;
        }
        if ($dataStatus > 0) {
            $params['dataStatus'] = $dataStatus;
        }

        return $this->sendDataRequest('queryAbnormalBettingData', $params, $pageIndex);
    }

    /**
     * 5.11 好路桌台
     * 
     * @param string $goodRoadTypes 好路类型（多个类型用,号隔开，不填则返回所有类型）
     * @return array
     */
    public function getGoodRoadTables($goodRoadTypes = '')
    {
        $params = [
            'timestamp' => time() * 1000,
        ];

        if (!empty($goodRoadTypes)) {
            $params['goodRoadTypes'] = $goodRoadTypes;
        }

        return $this->sendDataRequest('goodRoadTables', $params, 1);
    }

    /**
     * 5.12 查询单个会员在线状态
     * 
     * @param string $loginName 游戏账号
     * @return array
     */
    public function getPlayerOnlineStatus($loginName)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'timestamp' => time() * 1000,
        ];

        $headers = [
            'merchantCode: ' . $this->merchant_code
        ];

        $url = $this->getApiUrl('/data/merchant/playerIsOnline/v1');
        return $this->sendRequest($url, $params, 'POST', 'application/json', $headers);
    }

    /**
     * 5.13 主播排班
     * 
     * @param string $dayStr 当前时间（日期格式 yyyy-MM-dd）
     * @return array
     */
    public function getAnchorScheduling($dayStr = '')
    {
        if (empty($dayStr)) {
            $dayStr = date('Y-m-d');
        }

        $params = [
            'dayStr' => $dayStr,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('getAnchorSchedulingOfDate', $params, 1);
    }
}

