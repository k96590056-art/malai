<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;
use App\Models\Api;

/**
 * DB游戏接口类
 * 参考TgService的结构和规范
 */
class DbService
{
    protected $api_account;
    protected $sign_key;
    protected $api_url;

    public function __construct()
    {
        // 从系统配置获取DB接口相关配置
        $this->api_url = SystemConfig::getValue('db_api_url') ?? env('DB_API_URL');
        $this->api_account = SystemConfig::getValue('db_api_account') ?? env('DB_API_ACCOUNT');
        $this->sign_key = SystemConfig::getValue('db_api_secret') ?? env('DB_API_SECRET');
    }

    /**
     * 生成签名（按照DB接口规范）
     * 参考Java SignatureUtils.generateSignature方法
     * 
     * 算法步骤：
     * 1. 对参数按键名进行排序
     * 2. 拼接参数为 key=value& 格式，排除空值
     * 3. 去掉最后一个 & 字符
     * 4. 拼接密钥
     * 5. MD5加密并转为大写
     *
     * @param array $params 请求参数数组
     * @return string MD5加密后的签名（大写）
     */
    private function generateCode(Array $params)
    {
        // 1. 对参数按键名进行排序（TreeMap排序效果）
        ksort($params);
        
        // 2. 拼接参数为 key=value& 格式，排除空值
        $sb = '';
        foreach ($params as $key => $value) {
            // 排除空值的参数（null和空字符串）
            if ($value !== null && $value !== '') {
                $sb .= $key . '=' . $value . '&';
            }
        }
        
        // 3. 去掉最后一个 & 字符
        if (strlen($sb) > 0) {
            $sb = rtrim($sb, '&');
        }
        
        // 4. 拼接密钥
        $sb .= $this->sign_key;
        
        // 5. MD5加密并转为大写
        return strtoupper(md5($sb));
    }

    /**
     * 发送HTTP请求（表单格式）
     *
     * @param string $url
     * @param array $post_data
     * @return array
     */
    private function sendRequest($url, $post_data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($contents, TRUE);
        
        if (!$result || !is_array($result)) {
            Log::error('DB接口请求失败', [
                'url' => $url,
                'http_code' => $httpCode,
                'response' => $contents
            ]);
            return [
                'Code' => -1,
                'Message' => '返回数据解析失败',
                'Data' => null
            ];
        }

        return $result;
    }

    /**
     * 发送JSON格式HTTP请求（带签名Headers）
     *
     * @param string $url
     * @param array $body_data 请求体数据
     * @param array $headers 额外的请求头（可选）
     * @return array
     */
    private function sendJsonRequest($url, $body_data = [], $headers = [])
    {
        // 生成timestamp和nonce（确保每次请求都是唯一的）
        // timestamp使用10位秒级时间戳
        $microtime = microtime(true);
        $timestamp = (string)(int)$microtime; // 10位秒级时间戳
        
        // 生成6位唯一的nonce：使用微秒部分的后3位 + 3位随机数，确保唯一性
        // 从microtime中提取微秒部分（小数部分）转换为3位数字
        $microseconds = (int)(($microtime - floor($microtime)) * 1000); // 微秒部分转换为0-999的整数
        $microStr = str_pad((string)($microseconds % 1000), 3, '0', STR_PAD_LEFT); // 确保3位
        $randomStr = str_pad((string)rand(0, 999), 3, '0', STR_PAD_LEFT); // 3位随机数
        $nonce = $microStr . $randomStr; // 组合成6位唯一的nonce
        
        // 如果组合后不是6位，则使用纯随机数（备用方案）
        if (strlen($nonce) != 6) {
            $nonce = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        // 构建签名参数（签名需要包含body参数、timestamp和nonce）
        // 注意：签名时使用的key是timestamp和nonce，不是ob-timestamp和ob-nonce
        $signParams = array_merge($body_data, [
            'timestamp' => $timestamp,
            'nonce' => $nonce
        ]);

        // 生成签名
        $signature = $this->generateCode($signParams);

        // 设置请求头（请求头中使用ob-timestamp和ob-nonce）
        $requestHeaders = array_merge([
            'Content-Type: application/json',
            'merchantCode: ' . $this->api_account,
            'ob-timestamp: ' . $timestamp,
            'ob-nonce: ' . $nonce,
            'ob-signature: ' . $signature,
        ], $headers);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        $contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('DB接口请求CURL错误', [
                'url' => $url,
                'curl_error' => $curlError
            ]);
            return [
                'code' => -1,
                'message' => '请求失败：' . $curlError,
                'data' => null
            ];
        }

        // 记录请求和响应日志（用于调试）
        Log::info('DB接口请求详情', [
            'url' => $url,
            'http_code' => $httpCode,
            'request_body' => $body_data,
            'response_raw' => $contents,
            'response_length' => strlen($contents)
        ]);

        $result = json_decode($contents, TRUE);
        
        if (!$result || !is_array($result)) {
            Log::error('DB接口请求失败 - JSON解析失败', [
                'url' => $url,
                'http_code' => $httpCode,
                'response_raw' => $contents,
                'response_length' => strlen($contents),
                'json_error' => json_last_error_msg(),
                'request_body' => $body_data,
                'headers' => $requestHeaders
            ]);
            return [
                'code' => -1,
                'message' => '返回数据解析失败：' . json_last_error_msg(),
                'data' => null,
                'raw_response' => substr($contents, 0, 500) // 返回前500字符用于调试
            ];
        }

        return $result;
    }

    /**
     * 注册用户到DP游戏平台
     * 参考文档：https://apidoc.gtlboe.com/zh/member/MemberControllerApi.html
     * URL: /member/register/v1
     * Type: POST
     * Content-Type: application/json
     *
     * @param string $userName 玩家账号（唯一，必填，4-11位，至少两个字母加数字组合，字母都是小写）
     * @param string $password 密码（必填，默认123456）
     * @param string $currency 币种（必填，如VND-越南盾,CNY-人民币,THB-泰铢,USDT-泰达币，默认USDT）
     * @param string $lang 站点语言（必填，如zh_CN-中文,en_US-英文，默认zh_CN）
     * @return array
     */
    public function register($userName, $password = '123456', $currency = 'USDT', $lang = 'zh_CN')
    {
        Log::info('DB注册 - 开始调用', [
            'userName' => $userName,
            'api_url' => $this->api_url,
            'api_account' => $this->api_account,
            'currency' => $currency,
            'lang' => $lang
        ]);

        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        // 检查API URL是否配置
        if (empty($this->api_url)) {
            $return['code'] = 400;
            $return['message'] = 'DB API URL未配置';
            Log::error('DB API URL未配置');
            return $return;
        }

        // 确保用户名是小写
        $userName = strtolower($userName);

        // 构建请求体参数（JSON格式）
        $bodyData = [
            'userName' => $userName,
            'password' => $password,
            'currency' => $currency,
            'lang' => $lang,
        ];

        // 发送JSON请求到注册接口
        $apiUrl = rtrim($this->api_url, '/') . '/member/register/v1';
        Log::info('DB注册 - 请求参数', [
            'userName' => $userName,
            'body_data' => $bodyData,
            'full_url' => $apiUrl,
            'api_account' => $this->api_account
        ]);
        
        $res = $this->sendJsonRequest($apiUrl, $bodyData);
        
        Log::info('DB注册 - 接口返回', [
            'userName' => $userName,
            'response_code' => $res['code'] ?? 'unknown',
            'response_message' => $res['message'] ?? 'unknown',
            'full_response' => $res
        ]);
        
        // 检查响应结果（DB接口返回code=0表示成功）
        if (!isset($res['code']) || $res['code'] != 0) {
            $return['code'] = 201;
            $return['message'] = $res['message'] ?? ($res['Message'] ?? '注册失败');
            Log::error('DB注册失败', [
                'userName' => $userName,
                'request_data' => $bodyData,
                'response' => $res
            ]);
            return $return;
        }

        Log::info('DB注册成功', [
            'userName' => $userName,
            'response' => $res
        ]);

        return $return;
    }

    /**
     * 获取游戏连接（登录获取游戏地址）
     * 参考文档：https://apidoc.gtlboe.com/zh/member/MemberControllerApi.html
     * URL: /member/getLaunchURL/v1
     * Type: POST
     * Content-Type: application/json
     *
     * @param string $userName 玩家账号（必填）
     * @param string $currency 币种（必填，如VND-越南盾,CNY-人民币,THB-泰铢,USDT-泰达币，默认USDT）
     * @param string $venueCode 场馆编码（必填，参考数据字典code）
     * @param int $gameId 平台统一id（选填，从游戏列表接口中获取，默认0）
     * @param int $deviceType 设备类型（选填，1=pc，2=h5，3=ios，4=android，默认2）
     * @param string $lang 站点语言（必填，如zh_CN-中文,en_US-英文，默认zh_CN）
     * @param string $userClientIp 用户客户端IP（选填，用于游戏厂商优化访问线路）
     * @return array
     */
    public function login($userName, $venueCode = '', $currency = 'USDT', $gameId = 0, $deviceType = 2, $lang = 'zh_CN', $userClientIp = '')
    {
        Log::info('DB登录 - 开始调用', [
            'userName' => $userName,
            'venueCode' => $venueCode,
            'currency' => $currency,
            'gameId' => $gameId,
            'deviceType' => $deviceType,
            'lang' => $lang,
            'userClientIp' => $userClientIp,
            'api_url' => $this->api_url,
            'api_account' => $this->api_account
        ]);

        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        // 检查API URL是否配置
        if (empty($this->api_url)) {
            $return['code'] = 400;
            $return['message'] = 'DB API URL未配置';
            Log::error('DB API URL未配置');
            return $return;
        }

        // 验证必填参数
        if (empty($venueCode)) {
            $return['code'] = 400;
            $return['message'] = 'venueCode（场馆编码）不能为空';
            Log::error('DB登录参数错误 - venueCode为空', [
                'userName' => $userName
            ]);
            return $return;
        }

        // 确保用户名是小写
        $userName = strtolower($userName);

        // 构建请求体参数（JSON格式）
        $bodyData = [
            'userName' => $userName,
            'currency' => $currency,
            'venueCode' => $venueCode,
            'deviceType' => $deviceType,
            'lang' => $lang,
        ];

        // gameId选填，如果不为0则添加
        if ($gameId > 0) {
            $bodyData['gameId'] = $gameId;
        }

        // userClientIp选填，如果提供则添加
        if (!empty($userClientIp)) {
            $bodyData['userClientIp'] = $userClientIp;
        }

        // 发送JSON请求到获取游戏链接接口
        $apiUrl = rtrim($this->api_url, '/') . '/member/getLaunchURL/v1';
        Log::info('DB登录 - 请求参数', [
            'userName' => $userName,
            'venueCode' => $venueCode,
            'body_data' => $bodyData,
            'full_url' => $apiUrl,
            'api_account' => $this->api_account
        ]);
        
        $res = $this->sendJsonRequest($apiUrl, $bodyData);
        
        Log::info('DB登录 - 接口返回', [
            'userName' => $userName,
            'venueCode' => $venueCode,
            'gameId' => $gameId,
            'response_code' => $res['code'] ?? 'unknown',
            'response_message' => $res['message'] ?? 'unknown',
            'has_data' => isset($res['data']),
            'has_content' => isset($res['data']['content']),
            'full_response' => $res
        ]);
        
        // 检查响应结果（DB接口返回code=0表示成功）
        if (!isset($res['code']) || $res['code'] != 0) {
            $return['code'] = 201;
            $return['message'] = $res['message'] ?? ($res['Message'] ?? '获取游戏链接失败');
            Log::error('DB登录失败', [
                'userName' => $userName,
                'venueCode' => $venueCode,
                'gameId' => $gameId,
                'request_data' => $bodyData,
                'response' => $res
            ]);
            return $return;
        }

        // 获取游戏链接（content字段）
        $gameUrl = $res['data']['content'] ?? '';
        
        if (empty($gameUrl)) {
            $return['code'] = 201;
            $return['message'] = '获取游戏链接失败：响应中未包含游戏链接';
            Log::error('DB登录 - 获取游戏链接失败', [
                'userName' => $userName,
                'venueCode' => $venueCode,
                'gameId' => $gameId,
                'response_data' => $res['data'] ?? null,
                'full_response' => $res
            ]);
            return $return;
        }

        $return['data'] = $gameUrl;
        $return['traceId'] = $res['traceId'] ?? '';

        Log::info('DB登录成功', [
            'userName' => $userName,
            'venueCode' => $venueCode,
            'gameId' => $gameId,
            'game_url' => $gameUrl,
            'url_length' => strlen($gameUrl),
            'traceId' => $return['traceId']
        ]);

        return $return;
    }

    /**
     * 查询用户余额
     *
     * @param string $username 用户名
     * @return array
     */
    public function balance($username)
    {
        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        $data = [
            'username' => $username,
            'api_account' => $this->api_account,
        ];

        $data['code'] = $this->generateCode($data);
        
        $res = $this->sendRequest($this->api_url . "/api/balance", $data);
        
        if (!isset($res['Code']) || $res['Code'] != '0') {
            $return['code'] = 201;
            $return['message'] = $res['Message'] ?? '查询余额失败';
            Log::error('DB查询余额失败', [
                'username' => $username,
                'response' => $res
            ]);
            return $return;
        }

        $return['data'] = $res['Data']['balance'] ?? 0;

        return $return;
    }

    /**
     * 充值（转入游戏）
     *
     * @param string $username 用户名
     * @param float $amount 金额
     * @param string $transferno 转账订单号
     * @return array
     */
    public function deposit($username, $amount, $transferno)
    {
        $amount = floor($amount);
        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        $data = [
            'username' => $username,
            'api_account' => $this->api_account,
            'amount' => $amount,
            'transferno' => $transferno,
        ];

        $data['code'] = $this->generateCode($data);
        
        $res = $this->sendRequest($this->api_url . "/api/deposit", $data);
        
        if (!isset($res['Code']) || $res['Code'] != '0') {
            $return['code'] = 201;
            $return['message'] = $res['Message'] ?? '充值失败';
            Log::error('DB充值失败', [
                'username' => $username,
                'amount' => $amount,
                'transferno' => $transferno,
                'response' => $res
            ]);
            return $return;
        }

        Log::info('DB充值成功', [
            'username' => $username,
            'amount' => $amount,
            'transferno' => $transferno
        ]);

        return $return;
    }

    /**
     * 提现（转回钱包）
     *
     * @param string $username 用户名
     * @param float $amount 金额
     * @param string $transferno 转账订单号
     * @return array
     */
    public function withdrawal($username, $amount, $transferno)
    {
        $amount = floor($amount);
        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        $data = [
            'username' => $username,
            'api_account' => $this->api_account,
            'amount' => $amount,
            'transferno' => $transferno,
        ];

        $data['code'] = $this->generateCode($data);
        
        $res = $this->sendRequest($this->api_url . "/api/withdrawal", $data);
        
        if (!isset($res['Code']) || $res['Code'] != '0') {
            $return['code'] = 201;
            $return['message'] = $res['Message'] ?? '提现失败';
            Log::error('DB提现失败', [
                'username' => $username,
                'amount' => $amount,
                'transferno' => $transferno,
                'response' => $res
            ]);
            return $return;
        }

        Log::info('DB提现成功', [
            'username' => $username,
            'amount' => $amount,
            'transferno' => $transferno
        ]);

        return $return;
    }

    /**
     * 获取游戏列表
     * 参考文档：https://apidoc.gtlboe.com/zh/member/GameListApi.html
     * URL: /member/game/list/v1
     * Type: POST
     * Content-Type: application/json
     *
     * @param string $venueCode 场馆编码（必填，参考数据字典）
     * @param string $currency 币种（必填，参考数据字典，默认USDT）
     * @param int $pageNum 分页参数，默认第一页（0）
     * @param int $pageSize 分页参数，默认每页10条，最大500条
     * @return array
     */
    public function getGameList($venueCode = '', $currency = 'USDT', $pageNum = 0, $pageSize = 10)
    {
        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        // 构建请求体参数
        $bodyData = [
            'currency' => $currency,
            'venueCode' => $venueCode,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
        ];

        // 验证必填参数
        if (empty($venueCode)) {
            $return['code'] = 400;
            $return['message'] = 'venueCode（场馆编码）不能为空';
            Log::error('DB获取游戏列表参数错误', [
                'venueCode' => $venueCode,
                'currency' => $currency
            ]);
            return $return;
        }

        // 验证pageSize范围
        if ($pageSize > 500) {
            $pageSize = 500;
        }

        // 检查API URL是否配置
        if (empty($this->api_url)) {
            $return['code'] = 400;
            $return['message'] = 'DB API URL未配置';
            Log::error('DB API URL未配置');
            return $return;
        }

        // 发送JSON请求到游戏列表接口
        $apiUrl = rtrim($this->api_url, '/') . '/member/game/list/v1';
        Log::info('DB获取游戏列表请求', [
            'api_url' => $this->api_url,
            'full_url' => $apiUrl,
            'venueCode' => $venueCode,
            'currency' => $currency,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'body_data' => $bodyData,
            'api_account' => $this->api_account
        ]);

        $res = $this->sendJsonRequest($apiUrl, $bodyData);
        
        // 检查响应结果（DB接口返回code=0表示成功）
        if (!isset($res['code']) || $res['code'] != 0) {
            $return['code'] = 201;
            $return['message'] = $res['message'] ?? ($res['Message'] ?? '获取游戏列表失败');
            $return['raw_response'] = $res['raw_response'] ?? null; // 包含原始响应用于调试
            Log::error('DB获取游戏列表失败', [
                'venueCode' => $venueCode,
                'currency' => $currency,
                'request_data' => $bodyData,
                'api_url' => $apiUrl,
                'response' => $res,
                'response_code' => $res['code'] ?? 'unknown'
            ]);
            return $return;
        }

        // 返回游戏列表数据
        $return['data'] = $res['data'] ?? [];
        $return['traceId'] = $res['traceId'] ?? '';

        Log::info('DB获取游戏列表成功', [
            'venueCode' => $venueCode,
            'currency' => $currency,
            'total_record' => $res['data']['totalRecord'] ?? 0,
            'list_count' => isset($res['data']['list']) ? count($res['data']['list']) : 0
        ]);

        return $return;
    }

    /**
     * 获取游戏记录
     * 参考文档：https://apidoc.gtlboe.com/zh/member/GameRecordApi.html
     * URL: /member/game/record/v1
     * Type: POST
     * Content-Type: application/json
     *
     * @param string $userName 玩家账号（必填）
     * @param string $startDate 起始日期 格式：2021-01-15 00:00:00
     * @param string $endDate 结束日期 格式：2021-01-15 23:59:59
     * @param int $pageNum 分页参数，默认第一页（0）
     * @param int $pageSize 分页参数，默认每页10条，最大500条
     * @return array
     */
    public function getGameRecords($userName, $startDate, $endDate, $pageNum = 0, $pageSize = 100)
    {
        $return = [
            'code' => 200,
            'message' => '成功'
        ];

        // 构建请求体参数
        $bodyData = [
            'userName' => $userName,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
        ];

        // 验证必填参数
        if (empty($userName)) {
            $return['code'] = 400;
            $return['message'] = 'userName（玩家账号）不能为空';
            Log::error('DB获取游戏记录参数错误', [
                'userName' => $userName,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $return;
        }

        // 验证pageSize范围
        if ($pageSize > 500) {
            $pageSize = 500;
        }

        // 检查API URL是否配置
        if (empty($this->api_url)) {
            $return['code'] = 400;
            $return['message'] = 'DB API URL未配置';
            Log::error('DB API URL未配置');
            return $return;
        }

        // 确保用户名是小写
        $userName = strtolower($userName);
        $bodyData['userName'] = $userName;

        // 发送JSON请求到游戏记录接口
        $apiUrl = rtrim($this->api_url, '/') . '/member/game/record/v1';
        Log::info('DB获取游戏记录请求', [
            'api_url' => $this->api_url,
            'full_url' => $apiUrl,
            'userName' => $userName,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'body_data' => $bodyData,
            'api_account' => $this->api_account
        ]);

        $res = $this->sendJsonRequest($apiUrl, $bodyData);
        
        // 检查响应结果（DB接口返回code=0表示成功）
        if (!isset($res['code']) || $res['code'] != 0) {
            $return['code'] = 201;
            $return['message'] = $res['message'] ?? ($res['Message'] ?? '获取游戏记录失败');
            $return['raw_response'] = $res['raw_response'] ?? null; // 包含原始响应用于调试
            Log::error('DB获取游戏记录失败', [
                'userName' => $userName,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'request_data' => $bodyData,
                'api_url' => $apiUrl,
                'response' => $res,
                'response_code' => $res['code'] ?? 'unknown'
            ]);
            return $return;
        }

        // 返回游戏记录数据
        $return['data'] = $res['data'] ?? [];
        $return['traceId'] = $res['traceId'] ?? '';

        Log::info('DB获取游戏记录成功', [
            'userName' => $userName,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total_record' => $res['data']['totalRecord'] ?? 0,
            'list_count' => isset($res['data']['list']) ? count($res['data']['list']) : 0
        ]);

        return $return;
    }
}
