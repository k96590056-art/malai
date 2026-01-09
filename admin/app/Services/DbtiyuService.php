<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;
use App\Models\User_Api;
use Illuminate\Http\Request;

/**
 * DbtiyuService 体育投注平台接口类
 * 参考文档：tiyu.md
 * 
 * 注意：此类包含两部分功能：
 * 1. 主动调用API（用户登录、查询数据等）
 * 2. 处理体育平台发起的回调请求（加扣款、余额查询等）
 */
class DbtiyuService
{
    protected $api_url;
    protected $secret_key;
    protected $merchant_code;
    protected $db_code;

    public function __construct()
    {
        // 从系统配置获取接口相关配置
        $this->api_url = SystemConfig::getValue('dbtiyu_api_url') ?? env('DBTIYU_API_URL', '');
        $this->secret_key = SystemConfig::getValue('dbtiyu_secret_key') ?? env('DBTIYU_SECRET_KEY', '');
        $this->merchant_code = SystemConfig::getValue('dbtiyu_merchant_code') ?? env('DBTIYU_MERCHANT_CODE', '');
        $this->db_code = "DBTY";
    }

    /**
     * 生成游戏登录URL（首页）
     * 
     * @param string $token 用户token
     * @param array $params 额外参数
     * @return string 游戏URL
     */

    /**
     * 生成列表页URL
     * 
     * @param string $token 用户token
     * @param array $params 参数
     *   - mt1: 一级菜单类型（虚拟体育固定：900，其他从接口获取）
     *   - mt2: 二级菜单类型（虚拟体育不传，其他从接口获取）
     *   - m: 一级菜单标识（不推荐使用）
     *   - s: 二级菜单标识（不推荐使用）
     *   - sy: 是否展示首页页面（1代表去掉H5页面首页模块）
     * @return string 游戏URL
     */
    public function getListUrl($token, $params = [])
    {
        $queryParams = [
            'token' => $token,
            'tag' => '01',
            'label' => '1', // 开启列表和详情页跳转功能
            'tmsn' => time() * 1000,
        ];

        // 添加菜单类型参数（推荐使用）
        if (isset($params['mt1'])) {
            $queryParams['mt1'] = $params['mt1'];
        }
        if (isset($params['mt2'])) {
            $queryParams['mt2'] = $params['mt2'];
        }

        // 兼容历史参数（不推荐使用）
        if (isset($params['m'])) {
            $queryParams['m'] = $params['m'];
        }
        if (isset($params['s'])) {
            $queryParams['s'] = $params['s'];
        }

        // 是否展示首页页面
        if (isset($params['sy'])) {
            $queryParams['sy'] = $params['sy'];
        }

        return $this->buildUrl($queryParams);
    }

    /**
     * 生成详情页URL
     * 
     * @param string $token 用户token
     * @param array $params 参数
     *   - mid: 赛事ID（必传，需与csid、tid一起传递）
     *   - csid: 赛种ID（必传，需与mid、tid一起传递）
     *   - tid: 联赛ID（必传，需与mid、csid一起传递）
     *   - gotohash: sports-赛事id-联赛tid-球种csid（可选，与mid/csid/tid二选一）
     *   - sy: 是否展示首页页面（1代表去掉H5页面首页模块）
     * @return string 游戏URL
     */
    public function getDetailUrl($token, $params = [])
    {
        $queryParams = [
            'token' => $token,
            'tag' => '01',
            'label' => '1', // 开启列表和详情页跳转功能
            'tmsn' => time() * 1000,
        ];

        // 方式1：使用gotohash参数（推荐）
        if (isset($params['gotohash'])) {
            $queryParams['gotohash'] = $params['gotohash'];
        }
        // 方式2：使用mid、csid、tid参数（三个必须同时传）
        elseif (isset($params['mid']) && isset($params['csid']) && isset($params['tid'])) {
            $queryParams['mid'] = $params['mid'];
            $queryParams['csid'] = $params['csid'];
            $queryParams['tid'] = $params['tid'];
        } else {
            Log::warning('Dbtiyu 生成详情页URL缺少必要参数', $params);
        }

        // 是否展示首页页面
        if (isset($params['sy'])) {
            $queryParams['sy'] = $params['sy'];
        }

        return $this->buildUrl($queryParams);
    }

    /**
     * 生成跳转到指定赛种赛事列表的URL
     * 
     * @param string $token 用户token
     * @param string $menuId 菜单ID（从接口 /yewu11/v3/menu/init 获取）
     * @param bool $isEsports 是否为电竞菜单
     * @return string 游戏URL
     */
    public function getSportListUrl($token, $menuId, $isEsports = false)
    {
        // 构建tyobj参数
        if ($isEsports) {
            // 电竞菜单写死参数值: 2000
            $lv2 = '2000';
        } else {
            // 非电竞菜单: 球种 ${+id+100}2
            // 例如：id=1时，lv2=1012；id=2时，lv2=1022
            $lv2 = ($menuId + 100) . '2';
        }

        $menuObj = [
            'menu' => [
                'lv1' => '2', // 固定写死，代表TY项目今日
                'lv2' => $lv2
            ]
        ];

        // JSON转义后生成base64编码字符串
        $tyobj = base64_encode(json_encode($menuObj, JSON_UNESCAPED_UNICODE));

        $queryParams = [
            'token' => $token,
            'tag' => '01',
            'tmsn' => time() * 1000,
            'tyobj' => $tyobj,
        ];

        return $this->buildUrl($queryParams);
    }

    /**
     * 生成任务中心页面URL
     * 
     * @param string $token 用户token
     * @param bool $isApp 是否为APP内嵌（true时隐藏返回按钮）
     * @return string 游戏URL
     */
    public function getTaskCenterUrl($token, $isApp = false)
    {
        $queryParams = [
            'token' => $token,
            'tag' => '01',
            'tmsn' => time() * 1000,
        ];

        if ($isApp) {
            $queryParams['isAPP'] = 'true';
        }

        // 注意：任务中心URL路径需要根据实际部署情况调整
        $baseUrl = rtrim($this->api_url, '/');
        $path = '/#/activity_task/';
        
        return $baseUrl . $path . '?' . http_build_query($queryParams);
    }

    /**
     * 构建完整URL
     * 
     * @param array $params 查询参数
     * @return string 完整URL
     */
    private function buildUrl($params)
    {
        if (empty($this->api_url)) {
            Log::error('Dbtiyu API URL未配置');
            return '';
        }

        $baseUrl = rtrim($this->api_url, '/');
        $queryString = http_build_query($params);
        
        return $baseUrl . '/?' . $queryString;
    }

    /**
     * 验证token是否有效
     * 注意：此方法需要根据实际API文档实现
     * 
     * @param string $token 用户token
     * @return bool
     */
    public function verifyToken($token)
    {
        // TODO: 根据实际API文档实现token验证逻辑
        if (empty($token)) {
            return false;
        }
        
        // 这里可以调用API验证token
        // 目前仅做基础验证
        return strlen($token) > 0;
    }

    /**
     * 生成gotohash参数
     * 格式：sports-赛事id-联赛tid-球种csid
     * 
     * @param int $mid 赛事ID
     * @param int $tid 联赛ID
     * @param int $csid 球种ID
     * @return string gotohash值
     */
    public function generateGotohash($mid, $tid, $csid)
    {
        return "sports-{$mid}-{$tid}-{$csid}";
    }

    /**
     * 解析gotohash参数
     * 
     * @param string $gotohash gotohash值
     * @return array|null 解析后的数组 ['mid' => xxx, 'tid' => xxx, 'csid' => xxx]，失败返回null
     */
    public function parseGotohash($gotohash)
    {
        if (empty($gotohash)) {
            return null;
        }

        // 格式：sports-赛事id-联赛tid-球种csid
        $parts = explode('-', $gotohash);
        if (count($parts) !== 4 || $parts[0] !== 'sports') {
            return null;
        }

        return [
            'mid' => $parts[1],
            'tid' => $parts[2],
            'csid' => $parts[3],
        ];
    }

    /**
     * 生成签名
     * 签名算法：MD5(MD5(参数1 + "&" + 参数2 + "&" + ...) + "&" + key)
     * 
     * @param array $params 参与签名的参数数组，按文档要求的顺序
     * @return string 签名（小写）
     */
    private function generateSignature($params)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbtiyu 密钥未配置');
            return '';
        }

        // 将所有参数转为字符串并用&连接
        $paramString = implode('&', array_map('strval', $params));
        
        // 第一次MD5
        $firstMd5 = md5($paramString);
        
        // 第二次MD5：MD5(第一次MD5 + "&" + key)
        $signString = $firstMd5 . '&' . $this->secret_key;
        $signature = md5($signString);

        Log::info('Dbtiyu 生成签名', [
            'params' => $params,
            'param_string' => $paramString,
            'first_md5' => $firstMd5,
            'sign_string' => $signString,
            'signature' => $signature
        ]);

        return $signature;
    }

    /**
     * 验证签名
     * 
     * @param array $params 参与签名的参数数组
     * @param string $signature 待验证的签名
     * @return bool
     */
    private function verifySignature($params, $signature)
    {
        $calculatedSignature = $this->generateSignature($params);
        $isValid = (strtolower($calculatedSignature) === strtolower($signature));

        if (!$isValid) {
            Log::warning('Dbtiyu 签名验证失败', [
                'calculated' => $calculatedSignature,
                'received' => $signature,
                'params' => $params
            ]);
        }

        return $isValid;
    }

    /**
     * 发送HTTP请求
     * 
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param string $method 请求方法（POST/GET）
     * @param string $contentType Content-Type
     * @return array
     */
    private function sendRequest($url, $params = [], $method = 'POST', $contentType = 'application/x-www-form-urlencoded')
    {
        $startTime = microtime(true);

        Log::info('Dbtiyu 请求开始', [
            'url' => $url,
            'method' => $method,
            'content_type' => $contentType,
            'params' => $params,
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $ch = curl_init();
        
        if ($method === 'GET') {
            $queryString = http_build_query($params);
            $requestUrl = $url . '?' . $queryString;
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_POST, false);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            
            if ($contentType === 'application/json') {
                $requestBody = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
            } else {
                $requestBody = http_build_query($params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded'
                ]);
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        if ($curlError) {
            Log::error('Dbtiyu API请求CURL错误', [
                'url' => $url,
                'curl_error' => $curlError,
                'http_code' => $httpCode,
                'duration_ms' => $duration
            ]);
            return [
                'status' => false,
                'code' => '9001',
                'message' => '请求失败：' . $curlError
            ];
        }

        $responseData = json_decode($response, true);
        Log::info('Dbtiyu API响应', [
            'url' => $url,
            'http_code' => $httpCode,
            'response' => $responseData ?: $response,
            'duration_ms' => $duration
        ]);

        if ($httpCode !== 200) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'HTTP错误：' . $httpCode
            ];
        }

        // 将 API 返回的 msg 转换为 message
        if (is_array($responseData) && isset($responseData['msg']) && !isset($responseData['message'])) {
            $responseData['message'] = $responseData['msg'];
            unset($responseData['msg']);
        }

        return $responseData ?: [
            'status' => false,
            'code' => '9001',
            'message' => '响应解析失败'
        ];
    }

    /**
     * 获取API完整URL
     * 
     * @param string $path API路径
     * @return string
     */
    private function getApiUrl($path)
    {
        $baseUrl = rtrim($this->api_url, '/');
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }

    /**
     * 刷新用户余额
     * API地址：/api/user/refreshBalance
     * 
     * @param string $userName 用户名
     * @param string $timestamp 时间戳（13位）
     * @return array
     */
    public function refreshBalance($userName, $timestamp = null)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + userName + timestamp
        $signParams = [$this->merchant_code, $userName, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        $url = $this->getApiUrl('/api/user/refreshBalance');
        return $this->sendRequest($url, $params);
    }

    /**
     * 用户登录（注册和登录合并）
     * API地址：/api/user/login
     * 
     * @param string $userName 用户名
     * @param string $terminal 终端类型（pc/mobile）
     * @param float $balance 用户余额（可选）
     * @param string $currency 币种
     * @param string $callbackUrl 回调URL（可选）
     * @param string $timestamp 时间戳（13位）
     * @param string $stoken 用户会话（可选）
     * @param string $language 用户语种（可选）
     * @param string $ip 用户IP（可选）
     * @return array
     */
    /**
     * 用户登录
     * API地址：/api/user/login
     * 
     * @param string $userName 用户名
     * @param string $venueCode 游戏场馆编码（暂不使用，保留以匹配IndexController调用）
     * @param string $currency 货币类型，默认'USDT'
     * @param int $gameId 游戏ID（暂不使用，保留以匹配IndexController调用）
     * @param int $deviceType 设备类型，1=pc, 2=h5
     * @param string $lang 语言，默认'zh_CN'
     * @param string $userClientIp 用户IP
     * @return array
     */
    public function login($userName, $venueCode = '', $currency = 'USDT', $gameId = 0, $deviceType = 2, $lang = 'zh_CN', $userClientIp = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = time() * 1000;

        // 将deviceType转换为terminal：1=pc, 2=mobile
        $terminal = ($deviceType == 1) ? 'pc' : 'mobile';
        
        // 将lang转换为language格式（zh_CN -> zh）
        $language = $lang;
        if ($lang === 'zh_CN' || $lang === 'zh-CN') {
            $language = 'zh';
        } elseif ($lang === 'en_US' || $lang === 'en-US') {
            $language = 'en';
        }

        // 签名参数：merchantCode + userName + terminal + timestamp
        $signParams = [$this->merchant_code, $userName, $terminal, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'terminal' => $terminal,
            'currency' => $currency,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if (!empty($language)) {
            $params['language'] = $language;
        }
        if (!empty($userClientIp)) {
            $params['ip'] = $userClientIp;
        }

        $url = $this->getApiUrl('/api/user/login');
        $result = $this->sendRequest($url, $params);
        
        // 转换API响应格式为IndexController期望的格式
        if (isset($result['code'])) {
            // API返回code为'0000'表示成功
            if ($result['code'] == '0000' || $result['code'] === '0000') {
                // 提取data字段（可能是URL字符串或包含URL的对象）
                $urlData = $result['data'] ?? '';
                // 如果data是数组且包含url字段，提取url
                if (is_array($urlData) && isset($urlData['url'])) {
                    $urlData = $urlData['url'];
                }
                
                return [
                    'code' => 200,
                    'message' => $result['message'] ?? '成功',
                    'data' => $urlData
                ];
            } else {
                // 错误响应
                return [
                    'code' => is_numeric($result['code']) ? (int)$result['code'] : 500,
                    'message' => $result['message'] ?? '登录失败',
                    'data' => $result['data'] ?? null
                ];
            }
        }
        
        // 如果响应格式不符合预期，返回错误
        return [
            'code' => 500,
            'message' => $result['message'] ?? '登录失败：响应格式错误',
            'data' => null
        ];
    }

    /**
     * 踢出用户
     * API地址：/api/user/kickOutUser
     * 
     * @param string $userName 用户名
     * @param string $timestamp 时间戳（13位）
     * @return array
     */
    public function kickOutUser($userName, $timestamp = null)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + userName + timestamp
        $signParams = [$this->merchant_code, $userName, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        $url = $this->getApiUrl('/api/user/kickOutUser');
        return $this->sendRequest($url, $params);
    }

    /**
     * 查看用户是否在线
     * API地址：/api/user/checkUserOnline
     * 
     * @param string $userName 用户名
     * @param string $timestamp 时间戳（13位）
     * @return array
     */
    public function checkUserOnline($userName, $timestamp = null)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + userName + timestamp
        $signParams = [$this->merchant_code, $userName, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        $url = $this->getApiUrl('/api/user/checkUserOnline');
        return $this->sendRequest($url, $params);
    }

    /**
     * 用户创建
     * API地址：/api/user/create
     * 
     * @param string $userName 用户名
     * @param string $password 密码（保留参数以兼容IndexController调用，体育API不使用此参数）
     * @param string $api_code API代码（保留参数以兼容IndexController调用，体育API不使用此参数）
     * @param string $currency 币种
     * @param string $timestamp 时间戳（13位）
     * @param string $nickname 昵称（可选）
     * @param string $agentId 代理ID（可选）
     * @return array
     */
    public function register($userName, $password = '123456', $api_code = '', $currency = '1', $timestamp = null, $nickname = '', $agentId = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：userName + merchantCode + timestamp
        $signParams = [$userName, $this->merchant_code, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'currency' => $currency,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if (!empty($nickname)) {
            $params['nickname'] = $nickname;
        }
        if (!empty($agentId)) {
            $params['agentId'] = $agentId;
        }

        $url = $this->getApiUrl('/api/user/create');
        $result = $this->sendRequest($url, $params);
        
        // 记录注册请求结果
        Log::info('Dbtiyu 注册请求结果', [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'result' => $result,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        return $result;
    }

    /**
     * 设置用户多语言
     * API地址：/api/user/setUserLanguage
     * 
     * @param string $token 用户token
     * @param string $languageName 语种编码
     * @param string $requestId 用户登录requestId
     * @return array
     */
    public function setUserLanguage($token, $languageName, $requestId = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'token' => $token,
            'languageName' => $languageName
        ];

        $headers = [];
        if (!empty($requestId)) {
            $headers[] = 'requestId: ' . $requestId;
        }

        $url = $this->getApiUrl('/api/user/setUserLanguage');
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 查询在线会员列表
     * API地址：/api/userOnline/queryOnlineUserList
     * 
     * @param int $pageIndex 页数
     * @param int $pageSize 页面数量
     * @param string $sort 排序方式（1登录时间降序，2登录时间升序）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选，用于筛选）
     * @return array
     */
    public function queryOnlineUserList($pageIndex = 1, $pageSize = 20, $sort = '1', $timestamp = null, $userName = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + timestamp
        $signParams = [$this->merchant_code, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
            'sort' => $sort,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }

        $url = $this->getApiUrl('/api/userOnline/queryOnlineUserList');
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 查询投注记录列表
     * API地址：/api/bet/queryBetList
     * 
     * @param string $startTime 开始时间（13位时间戳）
     * @param string $endTime 结束时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @param int $sportId 赛种ID（可选）
     * @param int $tournamentId 联赛ID（可选）
     * @param int $settleStatus 结算状态（可选）
     * @param int $pageNum 页面编号（可选）
     * @param int $pageSize 每页条数（可选）
     * @param int $orderBy 排序字段（可选）
     * @param string $language 语言（可选）
     * @return array
     */
    public function queryBetList($startTime, $endTime, $timestamp = null, $userName = '', $sportId = 0, $tournamentId = 0, $settleStatus = 0, $pageNum = 1, $pageSize = 1000, $orderBy = 1, $language = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'startTime' => (string)$startTime,
            'endTime' => (string)$endTime,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }
        if ($sportId > 0) {
            $params['sportId'] = $sportId;
        }
        if ($tournamentId > 0) {
            $params['tournamentId'] = $tournamentId;
        }
        if ($settleStatus > 0) {
            $params['settleStatus'] = $settleStatus;
        }
        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }
        if ($orderBy > 0) {
            $params['orderBy'] = $orderBy;
        }
        if (!empty($language)) {
            $params['language'] = $language;
        }

        $url = $this->getApiUrl('/api/bet/queryBetList');
        return $this->sendRequest($url, $params);
    }

    /**
     * 查询单条投注记录
     * API地址：/api/bet/getBetDetail
     * 
     * @param string $orderNo 注单ID
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @return array
     */
    public function getBetDetail($orderNo, $timestamp = null, $userName = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + orderNo + timestamp
        $signParams = [$this->merchant_code, $orderNo, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'orderNo' => $orderNo,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }

        $url = $this->getApiUrl('/api/bet/getBetDetail');
        return $this->sendRequest($url, $params);
    }

    /**
     * 预约投注订单拉取
     * API地址：/api/bet/queryPreBetOrderList
     * 
     * @param string $startTime 开始时间（13位时间戳）
     * @param string $endTime 结束时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param int $preOrderStatus 预投注订单状态（可选）
     * @param int $billStatus 订单结算状态（可选）
     * @param int $pageNum 页面编号（可选）
     * @param int $pageSize 每页条数（可选）
     * @return array
     */
    public function queryPreBetOrderList($startTime, $endTime, $timestamp = null, $preOrderStatus = 0, $billStatus = 0, $pageNum = 1, $pageSize = 1000)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'startTime' => (string)$startTime,
            'endTime' => (string)$endTime,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if ($preOrderStatus > 0) {
            $params['preOrderStatus'] = $preOrderStatus;
        }
        if ($billStatus > 0) {
            $params['billStatus'] = $billStatus;
        }
        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }

        $url = $this->getApiUrl('/api/bet/queryPreBetOrderList');
        return $this->sendRequest($url, $params);
    }

    /**
     * 查询投注记录列表V2
     * API地址：/api/bet/queryBetListV2
     * 
     * @param string $startTime 开始时间（13位时间戳）
     * @param string $endTime 结束时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @param int $sportId 赛种ID（可选）
     * @param int $tournamentId 联赛ID（可选）
     * @param int $settleStatus 注单状态（可选）
     * @param int $pageNum 页面编号（可选）
     * @param int $pageSize 每页条数（可选）
     * @param int $orderBy 排序字段（可选）
     * @param string $language 语言（可选）
     * @return array
     */
    public function queryBetListV2($startTime, $endTime, $timestamp = null, $userName = '', $sportId = 0, $tournamentId = 0, $settleStatus = 0, $pageNum = 1, $pageSize = 1000, $orderBy = 1, $language = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'startTime' => (string)$startTime,
            'endTime' => (string)$endTime,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }
        if ($sportId > 0) {
            $params['sportId'] = $sportId;
        }
        if ($tournamentId > 0) {
            $params['tournamentId'] = $tournamentId;
        }
        if ($settleStatus > 0) {
            $params['settleStatus'] = $settleStatus;
        }
        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }
        if ($orderBy > 0) {
            $params['orderBy'] = $orderBy;
        }
        if (!empty($language)) {
            $params['language'] = $language;
        }

        $url = $this->getApiUrl('/api/bet/queryBetListV2');
        return $this->sendRequest($url, $params);
    }

    /**
     * 真人注单列表
     * API地址：/api/bet/zr/queryOrderList
     * 
     * @param string $startTime 起始时间（13位时间戳）
     * @param string $endTime 结束时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @param int $settleStatus 结算状态（可选）
     * @param int $orderBy 排序字段（可选）
     * @param int $pageNum 分页起始页码（可选）
     * @param int $pageSize 每页展示条数（可选）
     * @return array
     */
    public function queryZrOrderList($startTime, $endTime, $timestamp = null, $userName = '', $settleStatus = 0, $orderBy = 0, $pageNum = 1, $pageSize = 20)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }
        if ($settleStatus > 0) {
            $params['settleStatus'] = $settleStatus;
        }
        if ($orderBy > 0) {
            $params['orderBy'] = $orderBy;
        }
        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }

        $url = $this->getApiUrl('/api/bet/zr/queryOrderList');
        return $this->sendRequest($url, $params);
    }

    /**
     * 彩票注单列表
     * API地址：/api/bet/cp/queryOrderList
     * 
     * @param string $startTime 起始时间（13位时间戳）
     * @param string $endTime 结束时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @param int $settleStatus 注单状态（可选）
     * @param int $orderBy 排序字段（可选）
     * @param int $pageNum 分页起始页码（可选）
     * @param int $pageSize 每页展示条数（可选）
     * @return array
     */
    public function queryCpOrderList($startTime, $endTime, $timestamp = null, $userName = '', $settleStatus = 0, $orderBy = 0, $pageNum = 1, $pageSize = 20)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }
        if ($settleStatus > 0) {
            $params['settleStatus'] = $settleStatus;
        }
        if ($orderBy > 0) {
            $params['orderBy'] = $orderBy;
        }
        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }

        $url = $this->getApiUrl('/api/bet/cp/queryOrderList');
        return $this->sendRequest($url, $params);
    }

    /**
     * 电子游戏注单列表
     * API地址：/api/bet/qp/queryOrderList
     * 
     * @param string $startTime 起始时间（13位时间戳）
     * @param string $endTime 结束时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @param int $orderBy 排序字段（可选）
     * @param int $pageNum 页码（可选）
     * @param int $pageSize 分页大小（可选）
     * @return array
     */
    public function queryQpOrderList($startTime, $endTime, $timestamp = null, $userName = '', $orderBy = 0, $pageNum = 1, $pageSize = 1000)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }
        if ($orderBy > 0) {
            $params['orderBy'] = $orderBy;
        }
        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }

        $url = $this->getApiUrl('/api/bet/qp/queryOrderList');
        return $this->sendRequest($url, $params);
    }

    /**
     * 查询交易记录列表
     * API地址：/api/fund/queryTransferList
     * 
     * @param string $userName 用户名
     * @param string $startTime 查询起始时间（13位时间戳）
     * @param string $endTime 查询转账截止时间（13位时间戳）
     * @param string $timestamp 时间戳（13位）
     * @param int $pageNum 页面编号（可选）
     * @param int $pageSize 每页条数（可选）
     * @return array
     */
    public function queryTransferList($userName, $startTime, $endTime, $timestamp = null, $pageNum = 1, $pageSize = 10)
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + userName + startTime + endTime + timestamp
        $signParams = [$this->merchant_code, $userName, $startTime, $endTime, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'userName' => $userName,
            'merchantCode' => $this->merchant_code,
            'startTime' => (string)$startTime,
            'endTime' => (string)$endTime,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if ($pageNum > 0) {
            $params['pageNum'] = $pageNum;
        }
        if ($pageSize > 0) {
            $params['pageSize'] = $pageSize;
        }

        $url = $this->getApiUrl('/api/fund/queryTransferList');
        return $this->sendRequest($url, $params);
    }

    /**
     * 获取单条交易记录
     * API地址：/api/fund/getTransferRecord
     * 
     * @param string $transferId 交易ID（19位数字）
     * @param string $timestamp 时间戳（13位）
     * @param string $userName 用户名（可选）
     * @return array
     */
    public function getTransferRecord($transferId, $timestamp = null, $userName = '')
    {
        if (empty($this->api_url)) {
            return [
                'status' => false,
                'code' => '9001',
                'message' => 'API URL未配置'
            ];
        }

        $timestamp = $timestamp ?: (time() * 1000);

        // 签名参数：merchantCode + transferId + timestamp
        $signParams = [$this->merchant_code, $transferId, $timestamp];
        $signature = $this->generateSignature($signParams);

        $params = [
            'merchantCode' => $this->merchant_code,
            'transferId' => $transferId,
            'timestamp' => (string)$timestamp,
            'signature' => $signature
        ];

        if (!empty($userName)) {
            $params['userName'] = $userName;
        }

        $url = $this->getApiUrl('/api/fund/getTransferRecord');
        return $this->sendRequest($url, $params);
    }

    /**
     * 处理加扣款回调
     * 体育平台调用商户服务
     * 
     * @return array
     */
    public function transfer()
    {
        $request = request();
        
        Log::info('Dbtiyu transfer 回调请求参数', [
            'all_post_params' => $request->all(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);

        $userName = $request->input('userName', '');
        $bizType = $request->input('bizType', '');
        $merchantCode = $request->input('merchantCode', '');
        $transferId = $request->input('transferId', '');
        $amount = $request->input('amount', 0);
        $transferType = $request->input('transferType', ''); // 1加款,2扣款
        $orderStr = $request->input('orderStr', '');
        $timestamp = $request->input('timestamp', '');
        $signature = $request->input('signature', '');

        // 验证签名
        $signParams = [$userName, $bizType, $merchantCode, $transferId, $amount, $transferType, $timestamp];
        if (!$this->verifySignature($signParams, $signature)) {
            Log::error('Dbtiyu transfer 签名验证失败', [
                'params' => $signParams,
                'signature' => $signature
            ]);
            return [
                'code' => '5003',
                'message' => '验签失败'
            ];
        }

        // TODO: 处理加扣款逻辑
        // 这里需要根据实际业务逻辑处理加扣款
        
        return [
            'code' => '0000',
            'message' => '扣款成功!',
            'data' => [
                'balance' => '0.00'
            ]
        ];
    }

    /**
     * 回调玩家余额
     * 体育平台调用商户服务
     * 
     * @return array
     */
    public function balance()
    {
        $request = request();
        
        Log::info('Dbtiyu balance 回调请求参数', [
            'all_post_params' => $request->all(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);

        $merchantCode = $request->input('merchantCode', '');
        $userName = $request->input('userName', '');
        $timestamp = $request->input('timestamp', '');
        $signature = $request->input('signature', '');

        // 验证签名
        $signParams = [$merchantCode, $userName, $timestamp];
        if (!$this->verifySignature($signParams, $signature)) {
            Log::error('Dbtiyu balance 签名验证失败', [
                'params' => $signParams,
                'signature' => $signature
            ]);
            return [
                'code' => '5003',
                'message' => '验签失败'
            ];
        }

        try {
            // 从loginName中移除merchant_code前缀
            $api_user = $userName;
            if (!empty($this->merchant_code)) {
                $merchantCodeLower = strtolower($this->merchant_code);
                $userNameLower = strtolower($userName);
                if (strpos($userNameLower, $merchantCodeLower) === 0) {
                    $api_user = substr($userName, strlen($this->merchant_code));
                }
            }
            
            // 查找用户API记录
            $userApi = User_Api::where('api_user', $api_user)->where('api_code', $this->db_code)->first();
            
            if (!$userApi) {
                Log::warning('Dbtiyu balance 用户不存在', [
                    'userName' => $userName,
                    'api_user' => $api_user,
                    'api_code' => $this->db_code
                ]);
                return [
                    'code' => '2002',
                    'message' => '没有此玩家'
                ];
            }
            
            // 从数据库中获取余额
            $balance = $userApi->api_money ?? 0;
            $balance = round($balance, 2); // 保留两位小数

            return [
                'message' => '成功',
                'code' => '0000',
                'data' => number_format($balance, 2, '.', ''),
                'serverTime' => time() * 1000,
                'status' => true
            ];
        } catch (\Exception $e) {
            Log::error('Dbtiyu balance 处理异常', [
                'userName' => $userName,
                'error' => $e->getMessage()
            ]);
            return [
                'code' => '2002',
                'message' => '没有此玩家'
            ];
        }
    }

    /**
     * 回调交易状态
     * 体育平台调用商户服务
     * 
     * @return array
     */
    public function status()
    {
        $request = request();
        
        Log::info('Dbtiyu status 回调请求参数', [
            'all_post_params' => $request->all(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);

        $merchantCode = $request->input('merchantCode', '');
        $transferId = $request->input('transferId', '');
        $status = $request->input('status', ''); // 0:失败,1:成功
        $msg = $request->input('msg', '');
        $orderList = $request->input('orderList', '');
        $timestamp = $request->input('timestamp', '');

        // TODO: 处理交易状态回调逻辑

        return [
            'message' => '成功',
            'code' => '0000',
            'serverTime' => time() * 1000
        ];
    }
}

