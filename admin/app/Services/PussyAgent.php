<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;

/**
 * PussyAgent Pussy888游戏平台接口类
 * 参考文档：pussy.md
 * 
 * 注意：此类用于处理Pussy888平台的API接口调用
 */
class PussyAgent
{
    protected $api_url;
    protected $api_url_backup;
    protected $authcode;
    protected $secret_key;

    public function __construct()
    {
        // 从系统配置获取接口相关配置
        $this->api_url = SystemConfig::getValue('pussy_api_url') ?? env('PUSSY_API_URL', 'http://api.pussy888.com');
        $this->api_url_backup = SystemConfig::getValue('pussy_api_url_backup') ?? env('PUSSY_API_URL_BACKUP', 'http://api2.pussy888.com');
        $this->authcode = SystemConfig::getValue('pussy_authcode') ?? env('PUSSY_AUTHCODE', '');
        $this->secret_key = SystemConfig::getValue('pussy_secret_key') ?? env('PUSSY_SECRET_KEY', '');
    }

    /**
     * 生成MD5签名
     * 签名规则：md5((authcode + userName + time + secretKey).toLowerCase())
     * 
     * @param string $userName 用户名
     * @param int $time 13位时间戳（毫秒）
     * @return string MD5签名（大写）
     */
    private function generateSign($userName, $time)
    {
        $signString = $this->authcode . $userName . $time . $this->secret_key;
        return strtoupper(md5(strtolower($signString)));
    }

    /**
     * 生成订单查询签名
     * 签名规则：md5((authcode + orderid + time + secretKey).toLowerCase())
     * 
     * @param string $orderid 订单ID
     * @param int $time 13位时间戳（毫秒）
     * @return string MD5签名（大写）
     */
    private function generateOrderSign($orderid, $time)
    {
        $signString = $this->authcode . $orderid . $time . $this->secret_key;
        return strtoupper(md5(strtolower($signString)));
    }

    /**
     * 发送HTTP请求
     * 
     * @param string $url API地址
     * @param array $params 请求参数
     * @param string $method 请求方法（GET/POST）
     * @return array
     */
    private function sendRequest($url, $params = [], $method = 'GET')
    {
        // 生成13位时间戳（毫秒）
        $time = time() * 1000;

        // 根据接口类型生成签名
        if (isset($params['orderid'])) {
            $params['sign'] = $this->generateOrderSign($params['orderid'], $time);
        } else {
            $userName = $params['userName'] ?? '';
            $params['sign'] = $this->generateSign($userName, $time);
        }

        $params['time'] = $time;
        $params['authcode'] = $this->authcode;

        $ch = curl_init();

        if ($method === 'GET') {
            $queryString = http_build_query($params);
            $fullUrl = $url . '?' . $queryString;
            curl_setopt($ch, CURLOPT_URL, $fullUrl);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
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

        if ($curlError) {
            Log::error('Pussy API请求CURL错误', [
                'url' => $url,
                'curl_error' => $curlError
            ]);
            return [
                'code' => -1,
                'msg' => '请求失败：' . $curlError,
                'success' => false
            ];
        }

        // 记录请求和响应日志
        $logParams = $params;
        if (isset($logParams['PassWd'])) {
            $logParams['PassWd'] = '***';
        }
        if (isset($logParams['OldPassWd'])) {
            $logParams['OldPassWd'] = '***';
        }
        Log::info('Pussy API请求', [
            'url' => $url,
            'method' => $method,
            'http_code' => $httpCode,
            'request_params' => $logParams,
            'response' => $response
        ]);

        $result = json_decode($response, true);

        if (!$result || !is_array($result)) {
            Log::error('Pussy API响应解析失败', [
                'url' => $url,
                'http_code' => $httpCode,
                'response' => $response
            ]);
            return [
                'code' => -1,
                'msg' => '响应解析失败',
                'success' => false,
                'raw_response' => $response
            ];
        }

        return $result;
    }

    /**
     * 1. 生成玩家免密登录游戏URL
     * 
     * @param string $userName 玩家账号
     * @param string $password 玩家密码（MD5加密后的）
     * @return string 游戏登录URL
     */
    public function generateLoginUrl($userName, $password)
    {
        $baseUrl = 'https://pussy888.mobi/';
        return $baseUrl . '?acc=' . urlencode($userName) . '&pw=' . urlencode($password);
    }

    /**
     * 2. 用户或代理注册
     * 
     * @param string $userName 用户名
     * @param string $password 密码（长度≤17）
     * @param string $agent 玩家的代理（上一级）
     * @param string $name 用户昵称
     * @param string $tel 电话号码
     * @param string $memo 备注
     * @param int $userType 用户类型（1=正式玩家，100=代理级别）
     * @return array
     */
    public function addUser($userName, $password, $agent, $name, $tel, $memo, $userType = 1)
    {
        $params = [
            'action' => 'addUser',
            'userName' => $userName,
            'PassWd' => $password,
            'agent' => $agent,
            'Name' => $name,
            'Tel' => $tel,
            'Memo' => $memo,
            'UserType' => $userType,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/account/account.ashx?action=addUser';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 3. 修改用户信息
     * 
     * @param string $userName 用户名
     * @param string $oldPassword 原密码
     * @param string $newPassword 新密码
     * @param string $name 用户昵称
     * @param string $tel 电话号码
     * @param string $memo 备注
     * @return array
     */
    public function editUser($userName, $oldPassword, $newPassword, $name, $tel, $memo)
    {
        $params = [
            'action' => 'editUser',
            'userName' => $userName,
            'OldPassWd' => $oldPassword,
            'PassWd' => $newPassword,
            'Name' => $name,
            'Tel' => $tel,
            'Memo' => $memo,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/account/account.ashx?action=editUser';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 4. 充值上下分接口
     * 
     * @param string $action 操作类型（setServerScore=玩家，setAgentScore=代理）
     * @param string $orderid 系统内唯一订单ID号（最多50字符）
     * @param float $scoreNum 变更的金额（正数=加分，负数=扣分）
     * @param string $userName 目标用户名
     * @param string $actionUser 操作者用户名
     * @param string $actionIp 操作者IP地址
     * @return array
     */
    public function setScore($action, $orderid, $scoreNum, $userName, $actionUser, $actionIp)
    {
        $params = [
            'action' => $action,
            'orderid' => $orderid,
            'scoreNum' => $scoreNum,
            'userName' => $userName,
            'ActionUser' => $actionUser,
            'ActionIp' => $actionIp,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/account/setScore.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 5. 查询用户信息（嵌套层级）
     * 
     * @param string $userName 要查询的用户名
     * @return array
     */
    public function getSearchUserInfo($userName)
    {
        $params = [
            'action' => 'getSearchUserInfo',
            'userName' => $userName,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/account/account.ashx?action=getSearchUserInfo';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 6. 查询用户信息（简单用户信息）
     * 
     * @param string $userName 要查询的用户名
     * @return array
     */
    public function getUserInfo($userName)
    {
        $params = [
            'action' => 'getUserInfo',
            'userName' => $userName,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/account/account.ashx?action=getUserInfo';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 7. 查询代理或玩家列表
     * 
     * @param string $action 列表类型（playerList=玩家，agentList=代理）
     * @param string $userName 用户名
     * @param int $pageIndex 页码（默认1，每页20条）
     * @return array
     */
    public function getAccountList($action, $userName, $pageIndex = 1)
    {
        $params = [
            'action' => $action,
            'userName' => $userName,
            'pageIndex' => $pageIndex,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/getData/AccountList.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 8. 查询玩家每天输赢总账
     * 
     * @param string $userName 用户名
     * @param string $sDate 开始日期 YYYY-MM-DD
     * @param string $eDate 结束日期 YYYY-MM-DD
     * @return array
     */
    public function getAccountReport($userName, $sDate, $eDate)
    {
        $params = [
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/AccountReport.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 9. 代理总输赢报表
     * 
     * @param string $userName 代理名
     * @param string $sDate 开始日期 YYYY-MM-DD
     * @param string $eDate 结束日期 YYYY-MM-DD
     * @return array
     */
    public function getAgentMoneyLog($userName, $sDate, $eDate)
    {
        $params = [
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/AgentMoneyLog.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 10. 下属玩家或代理报表
     * 
     * @param string $userName 用户名
     * @param string $type 类型（ServerTotalReport=玩家，AgentTotalReport=代理）
     * @param string $sDate 开始日期 YYYY-MM-DD hh:mm:ss
     * @param string $eDate 结束日期 YYYY-MM-DD hh:mm:ss
     * @return array
     */
    public function getAgentTotalReport($userName, $type, $sDate, $eDate)
    {
        $params = [
            'userName' => $userName,
            'Type' => $type,
            'sDate' => $sDate,
            'eDate' => $eDate,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/AgentTotalReport.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 11. 查询玩家上下分明细
     * 
     * @param string $userName 用户名
     * @param string $sDate 开始日期 YYYY-MM-DD hh:mm:ss
     * @param string $eDate 结束日期 YYYY-MM-DD hh:mm:ss
     * @param int $pageIndex 页码（默认1）
     * @return array
     */
    public function getUserScoreLog($userName, $sDate, $eDate, $pageIndex = 1)
    {
        $params = [
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
            'pageIndex' => $pageIndex,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/UserscoreLog.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 12. 查询玩家游戏记录
     * 
     * @param string $userName 查询的用户名
     * @param string $sDate 开始日期 YYYY-MM-DD hh:mm:ss
     * @param string $eDate 结束日期 YYYY-MM-DD hh:mm:ss
     * @param int $pageIndex 页码（默认1）
     * @param int $pageSize 每页记录数（默认20，最大1000）
     * @return array
     */
    public function getGameLog($userName, $sDate, $eDate, $pageIndex = 1, $pageSize = 20)
    {
        $params = [
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/GameLog.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 13. 切换账号禁用/开启的状态
     * 
     * @param string $userName 目标用户名
     * @return array
     */
    public function disableUser($userName)
    {
        $params = [
            'action' => 'disable',
            'userName' => $userName,
        ];

        $url = rtrim($this->api_url, '/') . '/ashx/account/account.ashx?action=disable';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 14. 查询订单
     * 
     * @param string $orderid 订单ID
     * @return array
     */
    public function getOrder($orderid)
    {
        $params = [
            'orderid' => $orderid,
        ];

        $url = rtrim($this->api_url, '/') . '/api2URL/ashx/getOrder.ashx';
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * 解析错误代码
     * 
     * @param int $code 错误代码
     * @return string 错误说明
     */
    public function getErrorMessage($code)
    {
        $errorMessages = [
            0 => '成功',
            -1 => '账号已存在/账号或密码错误/其他错误/订单错误/异常错误/账号不存在',
            -2 => '签名错误',
            -3 => '正在操作，稍后再试',
            -4 => '操作太频繁，稍后再试',
            -5 => '分数参数错误',
            -6 => '系统异常，请稍后重试',
            -7 => '当前账号正在游戏中，不能操作分数',
            -8 => '余额不足',
            -9 => '账号不存在',
            -99 => '非法操作',
        ];

        return $errorMessages[$code] ?? '未知错误';
    }

    /**
     * 验证响应是否成功
     * 
     * @param array $response API响应
     * @return bool
     */
    public function isSuccess($response)
    {
        return isset($response['success']) && $response['success'] === true 
            && isset($response['code']) && $response['code'] == 0;
    }
}

