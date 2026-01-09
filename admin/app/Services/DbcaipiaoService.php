<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;

/**
 * DbcaipiaoService 彩票平台接口类
 * 参考文档：doc.md
 */
class DbcaipiaoService
{
    protected $api_url;
    protected $merchant;
    protected $secret_key;
    protected $db_code;

    public function __construct()
    {
        // 从系统配置获取接口相关配置
        $this->api_url = SystemConfig::getValue('dbcaipiao_api_url') ?? env('DBCAIPIAO_API_URL', '');
        $this->merchant = SystemConfig::getValue('dbcaipiao_merchant') ?? env('DBCAIPIAO_MERCHANT', '');
        $this->secret_key = SystemConfig::getValue('dbcaipiao_secret_key') ?? env('DBCAIPIAO_SECRET_KEY', '');
        $this->db_code = "DBCP";
    }

    /**
     * 生成MD5签名
     * 签名规则：
     * 1. 将参数按照参数名的首字母自然顺序进行排序（如果首字母相同则对比下一个字母，以此类推）
     * 2. 把排序后的 Key、Value 拼接成字符串（格式：key{value}key{value}...）
     * 3. 拼接商户密钥
     * 4. MD5编码
     *
     * @param array $params 请求参数数组（不包含sign）
     * @param bool $excludeSignParams 是否排除不参与签名的参数（如currencyType、loginIp等）
     * @return string MD5签名
     */
    private function generateSign($params, $excludeSignParams = [])
    {
        // 排除不参与签名的参数
        foreach ($excludeSignParams as $key) {
            unset($params[$key]);
        }

        // 移除sign参数（如果存在）
        unset($params['sign']);
        unset($params['signBack']);

        // 对参数按键名进行排序
        ksort($params);

        // 拼接参数为 key{value} 格式
        $signString = '';
        foreach ($params as $key => $value) {
            // 处理数组类型（如doubleList、normalList）
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $signString .= $key . $value;
        }

        // 拼接商户密钥
        $signString .= $this->secret_key;

        // MD5编码
        return md5($signString);
    }

    /**
     * 发送HTTP请求（JSON格式）
     *
     * @param string $url API地址
     * @param array $params 请求参数
     * @param string $method 请求方法（POST/GET）
     * @param string $contentType Content-Type（application/json 或 application/x-www-form-urlencoded）
     * @return array
     */
    private function sendRequest($url, $params = [], $method = 'POST', $contentType = 'application/json')
    {
        // 生成时间戳（如果不存在）
        if (!isset($params['timestamp'])) {
            $params['timestamp'] = time() * 1000; // 毫秒级时间戳
        }

        // 生成签名
        $params['sign'] = $this->generateSign($params);

        $ch = curl_init();

        if ($method === 'GET') {
            // GET请求：参数放在URL的?后面
            $queryString = http_build_query($params);
            $fullUrl = $url . '?' . $queryString;
            curl_setopt($ch, CURLOPT_URL, $fullUrl);
            curl_setopt($ch, CURLOPT_POST, false);
        } else {
            // POST请求
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            
            if ($contentType === 'application/json') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_UNESCAPED_UNICODE));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded'
                ]);
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('Dbcaipiao API请求CURL错误', [
                'url' => $url,
                'curl_error' => $curlError
            ]);
            return [
                'code' => -1,
                'msg' => '请求失败：' . $curlError
            ];
        }

        // 记录请求和响应日志
        $logParams = $params;
        if (isset($logParams['password'])) {
            $logParams['password'] = '***';
        }
        if (isset($logParams['secret_key'])) {
            $logParams['secret_key'] = '***';
        }
        Log::info('Dbcaipiao API请求', [
            'url' => $url,
            'method' => $method,
            'http_code' => $httpCode,
            'request_params' => $logParams,
            'response' => $response
        ]);

        $result = json_decode($response, true);

        if (!$result || !is_array($result)) {
            Log::error('Dbcaipiao API响应解析失败', [
                'url' => $url,
                'http_code' => $httpCode,
                'response' => $response
            ]);
            return [
                'code' => -1,
                'msg' => '响应解析失败',
                'raw_response' => $response
            ];
        }

        return $result;
    }

    /**
     * 一、玩家 API
     * 1. 玩家注册
     * API地址：/boracay/api/member/create
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $member 玩家账号（4-64字符，英文字母和数字，不分大小写）
     * @param int $memberType 玩家帐号类型（1：一般帐号；3：测试帐号）
     * @param string $password 玩家密码（已加密，长度<=50字符）
     * @param array $doubleList 双面盘代理模式各彩种对应返点（直客模式可为空）
     * @param array $normalList 标准盘代理模式各彩种对应返点（直客模式可为空）
     * @param int $currencyType 会员结算币种类型（不参与sign签名）
     * @return array
     */
    public function register($member, $memberType = 1, $password = '', $doubleList = [], $normalList = [], $currencyType = 1)
    {
        $params = [
            'member' => $member,
            'memberType' => $memberType,
            'password' => $password,
            'merchant' => $this->merchant,
            'doubleList' => $doubleList,
            'normalList' => $normalList,
            'timestamp' => time() * 1000,
            'currencyType' => $currencyType, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/member/create';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 2. 玩家登录
     * API地址：/boracay/api/member/login
     * 请求方法：POST
     * Content-Type：application/x-www-form-urlencoded
     *
     * @param string $member 玩家账号
     * @param string $password 玩家密码
     * @param string $loginIp 玩家登录IP（不参与sign签名）
     * @param string $returnUrl C端首页URL（不参与sign签名）
     * @param int $currentHallType 跳转地址的大厅类型（不参与sign签名，1：默认大厅；2：马来大厅；3：越南大厅；4：泰国大厅；5：台湾大厅）
     * @param int $lang 多语言类型（不参与sign签名）
     * @param int $ticketId 彩种id（不参与sign签名）
     * @param int $mcHideSize 玩家名称隐藏的长度（不参与sign签名）
     * @param int $colorTheme 颜色模式（不参与sign签名，1：日间模式；2：夜间模式）
     * @return array
     */
    public function login($member, $password, $loginIp = '', $returnUrl = '', $currentHallType = 1, $lang = 1, $ticketId = 0, $mcHideSize = 0, $colorTheme = 1)
    {
        $params = [
            'member' => $member,
            'password' => $password,
            'merchant' => $this->merchant,
            'timestamp' => time() * 1000,
            'loginIp' => $loginIp, // 不参与签名
            'returnUrl' => $returnUrl, // 不参与签名
            'currentHallType' => $currentHallType, // 不参与签名
            'lang' => $lang, // 不参与签名
            'ticketId' => $ticketId, // 不参与签名
            'mcHideSize' => $mcHideSize, // 不参与签名
            'colorTheme' => $colorTheme, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/member/login';
        return $this->sendRequest($url, $params, 'POST', 'application/x-www-form-urlencoded');
    }

    /**
     * 3. 玩家踢线
     * API地址：/boracay/api/member/offLine
     * 请求方法：POST
     * Content-Type：application/x-www-form-urlencoded
     *
     * @param string $member 玩家账号
     * @return array
     */
    public function kick($member)
    {
        $params = [
            'member' => $member,
            'merchant' => $this->merchant,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/member/offLine';
        return $this->sendRequest($url, $params, 'POST', 'application/x-www-form-urlencoded');
    }

    /**
     * 4. 玩家信息更新
     * API地址：/boracay/api/member/updateMember
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $member 玩家账号
     * @param string $password 玩家密码（已加密）
     * @param array $doubleList 双面盘代理模式各彩种对应返点（直客模式可为空）
     * @param array $normalList 标准盘代理模式各彩种对应返点（直客模式可为空）
     * @return array
     */
    public function updateUser($member, $password = '', $doubleList = [], $normalList = [])
    {
        $params = [
            'member' => $member,
            'password' => $password,
            'merchant' => $this->merchant,
            'doubleList' => $doubleList,
            'normalList' => $normalList,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/member/updateMember';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 二、非免转商户 钱包 API
     * 1. 玩家钱包转帐
     * API地址：/boracay/api/nofreemember/transferBalance
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $member 玩家账号
     * @param string $merchantAccount 商户账号
     * @param int $transferType 转账类型（1：从商户转入平台；2：从平台转回商户）
     * @param string $amount 转帐金额（精确到小数点后4位）
     * @param string $notifyId 讯息号（唯一，避免重复处理）
     * @return array
     */
    public function transfer($member, $merchantAccount, $transferType, $amount, $notifyId)
    {
        $params = [
            'member' => $member,
            'merchantAccount' => $merchantAccount,
            'transferType' => $transferType,
            'amount' => $amount,
            'notifyId' => $notifyId,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/nofreemember/transferBalance';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 2. 玩家转帐记录查询
     * API地址：/boracay/api/nofreemember/balanceRecords
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $member 要查询的玩家账号
     * @param string $merchant 要查询的商户账号
     * @param string $startTime 要查询的开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 要查询的结束时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $notifyId 要查询的讯息号（可选）
     * @param string $tradeType 要查询的转账类型（可选，1：从商户转入平台；2：从平台转回商户）
     * @param string $pageNum 页码（默认1）
     * @param string $pageSize 每页条目数量（默认100）
     * @return array
     */
    public function getBalanceRecords($member, $merchant, $startTime = '', $endTime = '', $notifyId = '', $tradeType = '', $pageNum = '1', $pageSize = '100')
    {
        $params = [
            'member' => $member,
            'merchant' => $merchant,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'notifyId' => $notifyId,
            'tradeType' => $tradeType,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/nofreemember/balanceRecords';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 3. 玩家余额查询
     * API地址：/boracay/api/nofreemember/balanceQuery
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $member 要查询的玩家账号
     * @param string $merchant 要查询的商户账号
     * @return array
     */
    public function balance($member, $merchant)
    {
        $params = [
            'member' => $member,
            'merchant' => $merchant,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/nofreemember/balanceQuery';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 三、免转商户 钱包 API
     * 3. 玩家安全上下分转帐
     * API地址：/boracay/api/safety/transfer
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $merchantCode 商户帐号
     * @param string $userName 玩家帐号
     * @param string $transferType 转帐类型（1：将玩家余额，从商户转入平台；2：将玩家余额，从平台转入商户）
     * @param string $amount 转帐金额（精确到小数点后4位）
     * @param string $transferId 转帐流水号（唯一）
     * @param string $safetyType 安全回调类型（1：同步回调；2：异步回调）
     * @return array
     */
    public function safetyTransfer($merchantCode, $userName, $transferType, $amount, $transferId, $safetyType = '1')
    {
        $params = [
            'merchantCode' => $merchantCode,
            'userName' => $userName,
            'transferType' => $transferType,
            'amount' => $amount,
            'transferId' => $transferId,
            'safetyType' => $safetyType,
            'timestamp' => (string)(time() * 1000), // 字符串类型
        ];

        // 安全上下分转帐使用二次MD5签名
        $signature = $this->generateSafetySignature($params);
        $params['signature'] = $signature;

        $url = rtrim($this->api_url, '/') . '/boracay/api/safety/transfer';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 生成安全上下分转帐的二次MD5签名
     * 格式：MD5(MD5({商户帐号}&{玩家帐号}&{转帐类型}&{转帐金额}&{转帐流水号}&{安全回调类型}&{时间戳})&{商户密钥})
     *
     * @param array $params
     * @return string
     */
    private function generateSafetySignature($params)
    {
        // 第一次签名：按顺序拼接参数值，用&连接
        $firstSignString = $params['merchantCode'] . '&' . 
                          $params['userName'] . '&' . 
                          $params['transferType'] . '&' . 
                          $params['amount'] . '&' . 
                          $params['transferId'] . '&' . 
                          $params['safetyType'] . '&' . 
                          $params['timestamp'];
        
        $firstSign = md5($firstSignString);

        // 第二次签名：第一次签名 + 商户密钥
        $secondSignString = $firstSign . '&' . $this->secret_key;
        return md5($secondSignString);
    }

    /**
     * 四、注单 API
     * 1. 已结算注单拉取
     * API地址：/merchantdata/pull/order
     * 请求方法：GET（参数放在URL的?后面）
     *
     * @param string $startTime 要查询的开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 要查询的结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param string $merchantAccount 要查询的商户账号
     * @param bool $agency 是否包含子商户的注单（true：查询merchantAccount及其子商户的注单；false：仅查询merchantAccount自己的注单）
     * @param int $pageSize 每页条目数量（不能大于1000）
     * @param int $lastOrderId 从哪一笔注单开始拉取数据（返回结果不包含此注单id，第一次查询填0）
     * @param int $lang 响应结果的多语言类型（不参与sign签名）
     * @return array
     */
    public function getGameRecords($startTime, $endTime, $merchantAccount, $agency = true, $pageSize = 100, $lastOrderId = 0, $lang = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'merchantAccount' => $merchantAccount,
            'agency' => $agency ? 'true' : 'false', // 转换为字符串
            'pageSize' => $pageSize,
            'lastOrderId' => $lastOrderId,
            'lang' => $lang, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/merchantdata/pull/order';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 2. 所有状态注单拉取
     * API地址：/merchantdata/pull/order/all
     * 请求方法：GET（参数放在URL的?后面）
     *
     * @param string $startTime 要查询的开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 要查询的结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param string $merchantAccount 要查询的商户账号
     * @param bool $agency 是否包含子商户的注单
     * @param int $pageSize 每页条目数量（不能大于1000）
     * @param int $lastOrderId 从哪一笔注单开始拉取数据
     * @param int $lang 响应结果的多语言类型（不参与sign签名）
     * @return array
     */
    public function getGameRecordsAll($startTime, $endTime, $merchantAccount, $agency = true, $pageSize = 100, $lastOrderId = 0, $lang = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'merchantAccount' => $merchantAccount,
            'agency' => $agency ? 'true' : 'false',
            'pageSize' => $pageSize,
            'lastOrderId' => $lastOrderId,
            'lang' => $lang, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/merchantdata/pull/order/all';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 3. 未结算注单拉取
     * API地址：/merchantdata/pull/order/unsettle
     * 请求方法：GET（参数放在URL的?后面）
     *
     * @param string $merchantAccount 要查询的商户账号
     * @param string $memberAccount 要查询的玩家账号
     * @param int $pageNum 页码（1为首页）
     * @param int $pageSize 每页条目数量（不能大于5000）
     * @param int $lang 响应结果的多语言类型（不参与sign签名）
     * @return array
     */
    public function getGameRecordsUnsettle($merchantAccount, $memberAccount, $pageNum = 1, $pageSize = 100, $lang = 1)
    {
        $params = [
            'merchantAccount' => $merchantAccount,
            'memberAccount' => $memberAccount,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'lang' => $lang, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/merchantdata/pull/order/unsettle';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 4. 已结算注单拉取 V2
     * API地址：/merchantdata/pull/v2/order
     * 请求方法：GET（参数放在URL的?后面）
     * 单日注单数100W+商户推荐使用
     * 时间范围不能超过5秒钟
     *
     * @param string $startTime 要查询的开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 要查询的结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过5秒）
     * @param string $merchantAccount 要查询的商户账号
     * @param bool $agency 是否包含子商户的注单
     * @param int $pageNum 页码（0为首页）
     * @param int $pageSize 每页条目数量（不能大于1000）
     * @param int $lang 响应结果的多语言类型（不参与sign签名）
     * @return array
     */
    public function getGameRecordsV2($startTime, $endTime, $merchantAccount, $agency = true, $pageNum = 0, $pageSize = 100, $lang = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'merchantAccount' => $merchantAccount,
            'agency' => $agency ? 'true' : 'false',
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'lang' => $lang, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/merchantdata/pull/v2/order';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 5. 所有状态注单拉取 V2
     * API地址：/merchantdata/pull/v2/order/all
     * 请求方法：GET（参数放在URL的?后面）
     * 单日注单数100W+商户推荐使用
     * 时间范围不能超过5秒钟
     *
     * @param string $startTime 要查询的开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 要查询的结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过5秒）
     * @param string $merchantAccount 要查询的商户账号
     * @param bool $agency 是否包含子商户的注单
     * @param int $pageNum 页码（0为首页）
     * @param int $pageSize 每页条目数量（不能大于1000）
     * @param int $lang 响应结果的多语言类型（不参与sign签名）
     * @return array
     */
    public function getGameRecordsAllV2($startTime, $endTime, $merchantAccount, $agency = true, $pageNum = 0, $pageSize = 100, $lang = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'merchantAccount' => $merchantAccount,
            'agency' => $agency ? 'true' : 'false',
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'lang' => $lang, // 不参与签名
        ];

        $url = rtrim($this->api_url, '/') . '/merchantdata/pull/v2/order/all';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 6. 所有状态注单拉取，不传语言参数时默认按下注时的语言翻译注单
     * API地址：/merchantdata/pull/order/translate/all
     * 请求方法：GET（参数放在URL的?后面）
     *
     * @param string $startTime 要查询的开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 要查询的结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param string $merchantAccount 要查询的商户账号
     * @param bool $agency 是否包含子商户的注单
     * @param int $pageSize 每页条目数量（不能大于1000）
     * @param int $lastOrderId 从哪一笔注单开始拉取数据
     * @param int $lang 响应结果的多语言类型（不参与sign签名，可选）
     * @return array
     */
    public function getGameRecordsTranslateAll($startTime, $endTime, $merchantAccount, $agency = true, $pageSize = 100, $lastOrderId = 0, $lang = null)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'merchantAccount' => $merchantAccount,
            'agency' => $agency ? 'true' : 'false',
            'pageSize' => $pageSize,
            'lastOrderId' => $lastOrderId,
        ];

        // lang参数可选，如果提供则添加
        if ($lang !== null) {
            $params['lang'] = $lang; // 不参与签名
        }

        $url = rtrim($this->api_url, '/') . '/merchantdata/pull/order/translate/all';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 五、其他 API
     * 1. 子商户创建
     * API地址：/boracay/api/merchant/add
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $merchantAccount 子商户账号（2-10位英文、数字、下划线）
     * @param string $merchantName 子商户名称（2-10位英文、数字、下划线）
     * @param string $parentMerchantAccount 父商户账号
     * @param int $brand 品牌类型（不参与sign签名，2：自有品牌；3：DB品牌；4：新DB品牌）
     * @return array
     */
    public function merchantAdd($merchantAccount, $merchantName, $parentMerchantAccount, $brand = null)
    {
        $params = [
            'merchantAccount' => $merchantAccount,
            'merchantName' => $merchantName,
            'parentMerchantAccount' => $parentMerchantAccount,
            'timestamp' => time() * 1000,
        ];

        // brand参数可选，不参与签名
        if ($brand !== null) {
            $params['brand'] = $brand;
        }

        $url = rtrim($this->api_url, '/') . '/boracay/api/merchant/add';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 2. 彩票长龙查询
     * API地址：/boracay/api/lottery/longDragon
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $merchantAccount 商户账号（用于签名验证）
     * @param array $ticketParams 要查询的彩种参数集合（格式：[{"ticketId": 48, "ticketName": "极速飞艇"}]）
     * @return array
     */
    public function lotteryLongDragon($merchantAccount, $ticketParams = [])
    {
        $params = [
            'merchantAccount' => $merchantAccount,
            'ticketParams' => $ticketParams,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/lottery/longDragon';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 3. 彩种数量查询
     * API地址：/boracay/api/lottery/ticketCount
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $merchantAccount 一级商户帐号
     * @return array
     */
    public function lotteryTicketCount($merchantAccount)
    {
        $params = [
            'merchantAccount' => $merchantAccount,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/lottery/ticketCount';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 4. 商户对帐
     * API地址：/merchantdata/reconciliation/
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param string $merchantAccount 商户帐号
     * @param int $containType 包含数据类型（1：包含下级商户数据；2：不包含下级商户数据）
     * @param string $startDate 要查询的开始日期（格式：yyyy-MM-dd）
     * @param string $endDate 要查询的结束日期（格式：yyyy-MM-dd）
     * @return array
     */
    public function reconciliation($merchantAccount, $containType, $startDate, $endDate)
    {
        $params = [
            'merchantAccount' => $merchantAccount,
            'containType' => $containType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/merchantdata/reconciliation/';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 5. 更新密钥请求
     * API地址：/boracay/api/merchant/secretKey/update
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param int $queryID 商户自定义单号（不超过32位数）
     * @param string $merchantAccount 要查询的商户账号
     * @param string $secretKey 备用密钥
     * @return array
     */
    public function secretKeyUpdate($queryID, $merchantAccount, $secretKey)
    {
        $params = [
            'queryID' => $queryID,
            'merchantAccount' => $merchantAccount,
            'secretKey' => $secretKey,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/merchant/secretKey/update';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * 6. 商户密钥查询
     * API地址：/boracay/api/merchant/secretKey/query
     * 请求方法：POST
     * Content-Type：application/json
     *
     * @param int $queryID 商户自定义单号（不超过32位数）
     * @param string $merchantAccount 要查询的商户账号
     * @param string $secretKey 备用密钥
     * @return array
     */
    public function secretKeyQuery($queryID, $merchantAccount, $secretKey)
    {
        $params = [
            'queryID' => $queryID,
            'merchantAccount' => $merchantAccount,
            'secretKey' => $secretKey,
            'timestamp' => time() * 1000,
        ];

        $url = rtrim($this->api_url, '/') . '/boracay/api/merchant/secretKey/query';
        return $this->sendRequest($url, $params, 'POST', 'application/json');
    }

    /**
     * ========================================
     * 三、免转商户 钱包 API
     * ========================================
     */

    /**
     * 1. 玩家钱包加/扣款（接收平台请求）
     * API地址：/xx/updateBalance（由免转商户提供，需对平台IP加白）
     * 请求方法：POST
     * Content-Type：application/json
     * 
     * 注意：
     * - 玩家投注：由平台向商户发起注单扣款请求，如请求超时或非【所有】交易皆成功，平台一律视为所有注单投注失败
     * - 投注扣款：请求参数中timestamp为发起请求时间，如果接收到时间大于10秒判定请求超时，不需要扣款
     * - 撤单/派奖：商户返回的交易数据处理结果中，如有失败，则平台会一直定时向商户发起请求
     * - 一笔注单（orderId）对应的每一个帐变类型（transferType），在商户侧都只能有一笔处理成功记录（幂等性）
     *
     * @param array $requestData 平台请求数据
     *   - transferType: String 帐变类型（1:投注扣款, 2:撤单加款, 3:派奖加款, 4:撤回派奖扣款, 5:二次派奖加款, 6:投注返点加款, 7:撤回投注返点扣款）
     *   - notifyId: String 讯息号
     *   - timestamp: String 当前时间时间戳
     *   - sign: String MD5签名
     *   - signBack: String 备用商户密钥的MD5签名
     *   - transferDatas: JSON数组 交易数据集合（不参与sign签名）
     *     - orderId: String 注单id
     *     - member: String 玩家帐号
     *     - amount: Number 帐变金额
     * @param callable $handler 处理余额变更的回调函数 function($orderId, $member, $amount, $transferType) { return ['code' => 0, 'beforeBalance' => '100.0000', 'balance' => '90.0000']; }
     * @return array 响应结果
     *   - code: Number 响应状态码（200为成功）
     *   - msg: String 响应结果说明
     *   - data: JSON数组 交易数据处理结果集合
     */
    public function updateBalance($requestData, callable $handler)
    {
        // 验证签名
        if (!$this->verifyWalletSign($requestData)) {
            Log::error('Dbcaipiao 钱包加扣款签名验证失败', ['request_data' => $requestData]);
            return [
                'code' => 403,
                'msg' => '签名验证失败',
                'data' => []
            ];
        }

        // 检查请求超时（投注扣款时，如果接收到时间大于10秒判定请求超时）
        $transferType = $requestData['transferType'] ?? '';
        $timestamp = $requestData['timestamp'] ?? '';
        if ($transferType == '1' && !empty($timestamp)) {
            $requestTime = intval($timestamp) / 1000; // 转换为秒级时间戳
            $currentTime = time();
            $diffTime = $currentTime - $requestTime;
            if ($diffTime > 10) {
                Log::warning('Dbcaipiao 钱包加扣款请求超时', [
                    'transfer_type' => $transferType,
                    'diff_time' => $diffTime,
                    'request_time' => date('Y-m-d H:i:s', $requestTime),
                    'current_time' => date('Y-m-d H:i:s', $currentTime)
                ]);
                // 超时时不处理，但返回成功（避免平台重试）
                $transferDatas = $requestData['transferDatas'] ?? [];
                $data = [];
                foreach ($transferDatas as $transferData) {
                    $data[] = [
                        'code' => 1, // 失败
                        'orderId' => $transferData['orderId'] ?? '',
                        'member' => $transferData['member'] ?? '',
                        'amount' => $transferData['amount'] ?? '0.0000',
                        'beforeBalance' => '0.0000',
                        'balance' => '0.0000'
                    ];
                }
                return [
                    'code' => 200,
                    'msg' => '请求超时',
                    'data' => $data
                ];
            }
        }

        // 处理交易数据
        $transferDatas = $requestData['transferDatas'] ?? [];
        $results = [];
        $allSuccess = true;
        $hasInsufficientBalance = false;

        foreach ($transferDatas as $transferData) {
            $orderId = $transferData['orderId'] ?? '';
            $member = $transferData['member'] ?? '';
            $amount = $transferData['amount'] ?? 0;

            if (empty($orderId) || empty($member)) {
                $results[] = [
                    'code' => 1, // 失败
                    'orderId' => $orderId,
                    'member' => $member,
                    'amount' => number_format($amount, 4, '.', ''),
                    'beforeBalance' => '0.0000',
                    'balance' => '0.0000'
                ];
                $allSuccess = false;
                continue;
            }

            // 调用回调函数处理余额变更
            try {
                $result = $handler($orderId, $member, $amount, $transferType);
                
                // 投注（扣款）时，如果任何一笔失败，所有注单都失败
                if ($transferType == '1') {
                    if ($result['code'] == 4) {
                        // 玩家余额不足
                        $hasInsufficientBalance = true;
                    } elseif ($result['code'] != 0) {
                        // 其他失败
                        $allSuccess = false;
                    }
                } else {
                    if ($result['code'] != 0) {
                        $allSuccess = false;
                    }
                }

                $results[] = [
                    'code' => $result['code'] ?? 1,
                    'orderId' => $orderId,
                    'member' => $member,
                    'amount' => number_format($amount, 4, '.', ''),
                    'beforeBalance' => isset($result['beforeBalance']) ? number_format($result['beforeBalance'], 4, '.', '') : '0.0000',
                    'balance' => isset($result['balance']) ? number_format($result['balance'], 4, '.', '') : '0.0000'
                ];
            } catch (\Exception $e) {
                Log::error('Dbcaipiao 钱包加扣款处理异常', [
                    'order_id' => $orderId,
                    'member' => $member,
                    'error' => $e->getMessage()
                ]);
                $results[] = [
                    'code' => 1, // 失败
                    'orderId' => $orderId,
                    'member' => $member,
                    'amount' => number_format($amount, 4, '.', ''),
                    'beforeBalance' => '0.0000',
                    'balance' => '0.0000'
                ];
                $allSuccess = false;
            }
        }

        // 投注（扣款）时，如果玩家余额不足，将所有注单结果改为4
        if ($transferType == '1' && $hasInsufficientBalance) {
            foreach ($results as &$result) {
                if ($result['code'] != 0) {
                    $result['code'] = 4; // 玩家余额不足
                }
            }
            unset($result);
        }
        // 投注（扣款）时，如果任何一笔失败（除了余额不足），将所有注单结果改为1
        elseif ($transferType == '1' && !$allSuccess && !$hasInsufficientBalance) {
            foreach ($results as &$result) {
                if ($result['code'] != 0 && $result['code'] != 4) {
                    $result['code'] = 1; // 失败
                }
            }
            unset($result);
        }

        Log::info('Dbcaipiao 钱包加扣款处理完成', [
            'transfer_type' => $transferType,
            'notify_id' => $requestData['notifyId'] ?? '',
            'results_count' => count($results),
            'all_success' => $allSuccess
        ]);

        return [
            'code' => 200, // 商户侧有正常收到请求并处理，就返回200
            'msg' => '成功',
            'data' => $results
        ];
    }

    /**
     * 验证钱包加扣款的签名
     * 签名规则：按照参数名的首字母自然顺序进行排序，把排序后的Key、Value拼接成字符串（格式：key{value}key{value}...），进行MD5编码
     * 注意：transferDatas不参与sign签名
     *
     * @param array $requestData
     * @return bool
     */
    private function verifyWalletSign($requestData)
    {
        // 提取不参与签名的参数
        $transferDatas = $requestData['transferDatas'] ?? null;
        unset($requestData['transferDatas']);

        // 移除sign和signBack
        $sign = $requestData['sign'] ?? '';
        $signBack = $requestData['signBack'] ?? '';
        unset($requestData['sign']);
        unset($requestData['signBack']);

        // 对参数按键名进行排序
        ksort($requestData);

        // 拼接参数为 key{value} 格式
        $signString = '';
        foreach ($requestData as $key => $value) {
            $signString .= $key . $value;
        }

        // 拼接商户密钥
        $signString .= $this->secret_key;

        // MD5编码
        $calculatedSign = md5($signString);

        // 验证签名（支持主密钥和备用密钥）
        if (strtolower($calculatedSign) === strtolower($sign)) {
            return true;
        }

        // 如果有备用密钥，验证备用密钥签名
        if (!empty($signBack)) {
            // TODO: 如果有备用密钥配置，这里需要验证signBack
            // 暂时只验证主密钥
        }

        return false;
    }

    /**
     * 2. 玩家余额查询（接收平台请求）
     * API地址：/xx/getBalance（由免转商户提供，需对平台IP加白）
     * 请求方法：POST
     * Content-Type：application/json
     * 注：需支持批量操作
     *
     * @param array $requestData 平台请求数据
     *   - members: JSON数组 要查询的玩家帐号集合
     *   - timestamp: String 当前时间时间戳
     *   - sign: String MD5签名
     *   - signBack: String 备用商户密钥的MD5签名
     * @param callable $handler 处理余额查询的回调函数 function($member) { return '100.0000'; }
     * @return array 响应结果
     *   - code: Number 响应状态码（200为成功）
     *   - msg: String 响应结果说明
     *   - data: JSON数组 结果数据集合
     *     - member: String 玩家帐号
     *     - balance: String 玩家钱包余额
     */
    public function getBalance($requestData, callable $handler)
    {
        // 验证签名
        if (!$this->verifyBalanceSign($requestData)) {
            Log::error('Dbcaipiao 余额查询签名验证失败', ['request_data' => $requestData]);
            return [
                'code' => 403,
                'msg' => '签名验证失败',
                'data' => []
            ];
        }

        // 处理查询
        $members = $requestData['members'] ?? [];
        $results = [];

        foreach ($members as $member) {
            try {
                $balance = $handler($member);
                $results[] = [
                    'member' => $member,
                    'balance' => number_format($balance, 4, '.', '')
                ];
            } catch (\Exception $e) {
                Log::error('Dbcaipiao 余额查询处理异常', [
                    'member' => $member,
                    'error' => $e->getMessage()
                ]);
                // 查询失败时返回0余额
                $results[] = [
                    'member' => $member,
                    'balance' => '0.0000'
                ];
            }
        }

        Log::info('Dbcaipiao 余额查询处理完成', [
            'members_count' => count($members),
            'results_count' => count($results)
        ]);

        return [
            'code' => 200,
            'msg' => '成功',
            'data' => $results
        ];
    }

    /**
     * 验证余额查询的签名
     * 签名规则：members{玩家帐号集合字串}timestamp{时间戳}{商户密钥}
     * 注意：members数组需要转换为字符串格式，如 [player001, player002, player003]
     *
     * @param array $requestData
     * @return bool
     */
    private function verifyBalanceSign($requestData)
    {
        // 提取members和timestamp
        $members = $requestData['members'] ?? [];
        $timestamp = $requestData['timestamp'] ?? '';
        $sign = $requestData['sign'] ?? '';
        $signBack = $requestData['signBack'] ?? '';

        // 将members数组转换为字符串格式（按照文档示例格式）
        $membersString = '[' . implode(', ', $members) . ']';

        // 拼接签名字符串：members{玩家帐号集合字串}timestamp{时间戳}{商户密钥}
        $signString = 'members' . $membersString . 'timestamp' . $timestamp . $this->secret_key;

        // MD5编码
        $calculatedSign = md5($signString);

        // 验证签名
        if (strtolower($calculatedSign) === strtolower($sign)) {
            return true;
        }

        // 如果有备用密钥，验证备用密钥签名
        if (!empty($signBack)) {
            // TODO: 如果有备用密钥配置，这里需要验证signBack
            // 暂时只验证主密钥
        }

        return false;
    }

    /**
     * 3. 玩家安全上下分转账（向平台发起）
     * 注意：此方法已存在（第387行），用于向平台发起转账请求
     * API地址：/boracay/api/safety/transfer
     * 该方法已经实现，符合文档要求
     */

    /**
     * 4. 玩家安全上下分转账回调（接收平台回调）
     * API地址：/xx/safetyTransfer（由免转商户提供，需对平台IP加白）
     * 请求方法：POST
     * Content-Type：application/json
     * 
     * 说明：当商户侧调用玩家安全上下分转账API时，平台会调用此API向商户确认相关订单是否存在
     *
     * @param array $requestData 平台请求数据
     *   - merchantCode: String 商户帐号
     *   - userName: String 玩家帐号
     *   - transferType: String 转帐类型（1:从商户转入平台, 2:从平台转入商户）
     *   - amount: String 转帐金额
     *   - transferId: String 转帐流水号
     *   - timestamp: String 当前时间时间戳
     *   - signature: String 二次MD5签名
     *   - signBack: String 备用商户密钥的二次MD5签名
     * @param callable $handler 处理转账确认的回调函数 function($transferId, $userName, $transferType, $amount) { return true; } // 返回true表示订单存在，false表示不存在
     * @return array 响应结果
     *   - code: String 响应状态码（0000为成功，其他皆为失败）
     *   - msg: String 响应结果说明
     *   - serverTime: String 当前时间时间戳（可选）
     */
    public function safetyTransferCallback($requestData, callable $handler)
    {
        // 验证签名
        if (!$this->verifySafetyTransferCallbackSign($requestData)) {
            Log::error('Dbcaipiao 安全上下分转账回调签名验证失败', ['request_data' => $requestData]);
            return [
                'code' => '405', // 注单不存在或签名失败
                'msg' => '签名验证失败',
                'serverTime' => (string)(time() * 1000)
            ];
        }

        // 提取参数
        $merchantCode = $requestData['merchantCode'] ?? '';
        $userName = $requestData['userName'] ?? '';
        $transferType = $requestData['transferType'] ?? '';
        $amount = $requestData['amount'] ?? '';
        $transferId = $requestData['transferId'] ?? '';

        if (empty($transferId) || empty($userName) || empty($transferType) || empty($amount)) {
            Log::error('Dbcaipiao 安全上下分转账回调参数不完整', ['request_data' => $requestData]);
            return [
                'code' => '405',
                'msg' => '参数不完整',
                'serverTime' => (string)(time() * 1000)
            ];
        }

        // 调用回调函数确认订单是否存在
        try {
            $exists = $handler($transferId, $userName, $transferType, $amount);
            
            if ($exists) {
                Log::info('Dbcaipiao 安全上下分转账回调确认订单存在', [
                    'transfer_id' => $transferId,
                    'user_name' => $userName,
                    'transfer_type' => $transferType,
                    'amount' => $amount
                ]);
                
                return [
                    'code' => '0000',
                    'msg' => '成功',
                    'serverTime' => (string)(time() * 1000)
                ];
            } else {
                Log::warning('Dbcaipiao 安全上下分转账回调订单不存在', [
                    'transfer_id' => $transferId,
                    'user_name' => $userName
                ]);
                
                return [
                    'code' => '405', // 注单不存在
                    'msg' => '订单不存在',
                    'serverTime' => (string)(time() * 1000)
                ];
            }
        } catch (\Exception $e) {
            Log::error('Dbcaipiao 安全上下分转账回调处理异常', [
                'transfer_id' => $transferId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'code' => '405',
                'msg' => '处理异常：' . $e->getMessage(),
                'serverTime' => (string)(time() * 1000)
            ];
        }
    }

    /**
     * 验证安全上下分转账回调的签名
     * 签名规则：二次MD5签名
     * 格式：MD5(MD5({商户帐号}&{玩家帐号}&{转帐类型}&{转帐金额}&{转帐流水号}&{时间戳})&{商户密钥})
     *
     * @param array $requestData
     * @return bool
     */
    private function verifySafetyTransferCallbackSign($requestData)
    {
        // 提取参数
        $merchantCode = $requestData['merchantCode'] ?? '';
        $userName = $requestData['userName'] ?? '';
        $transferType = $requestData['transferType'] ?? '';
        $amount = $requestData['amount'] ?? '';
        $transferId = $requestData['transferId'] ?? '';
        $timestamp = $requestData['timestamp'] ?? '';
        $signature = $requestData['signature'] ?? '';
        $signBack = $requestData['signBack'] ?? '';

        // 第一次签名：按顺序拼接参数值，用&连接
        $firstSignString = $merchantCode . '&' . 
                          $userName . '&' . 
                          $transferType . '&' . 
                          $amount . '&' . 
                          $transferId . '&' . 
                          $timestamp;
        
        $firstSign = md5($firstSignString);

        // 第二次签名：第一次签名 + 商户密钥
        $secondSignString = $firstSign . '&' . $this->secret_key;
        $calculatedSignature = md5($secondSignString);

        // 验证签名
        if (strtolower($calculatedSignature) === strtolower($signature)) {
            return true;
        }

        // 如果有备用密钥，验证备用密钥签名
        if (!empty($signBack)) {
            // TODO: 如果有备用密钥配置，这里需要验证signBack
            // 暂时只验证主密钥
        }

        return false;
    }
}


