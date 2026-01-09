<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class YesAgent
{
    protected $apiUrl = 'https://api.yes918.com/ashx';
    protected $agentId;
    protected $authCode;
    protected $secretKey;

    /**
     * YesAgent constructor.
     * @param string $agentId 代理ID
     * @param string $authCode 认证码
     * @param string $secretKey 密钥
     */
    public function __construct($agentId = null, $authCode = null, $secretKey = null)
    {
        $this->agentId = $agentId;
        $this->authCode = $authCode;
        $this->secretKey = $secretKey;
    }

    /**
     * 生成签名（标准方式：authcode + userName + time + secretKey）
     * @param string $userName 用户名
     * @param int $time 时间戳
     * @return string 签名（大写MD5）
     */
    private function generateSign($userName, $time)
    {
        $key = $this->authCode . $userName . $time . $this->secretKey;
        $key = strtolower($key);
        $sign = md5($key);
        return strtoupper($sign);
    }

    /**
     * 生成签名（特殊方式：authcode + time + secretKey，用于getOrder和kick）
     * @param int $time 时间戳
     * @return string 签名（大写MD5）
     */
    private function generateSignSpecial($time)
    {
        $key = $this->authCode . $time . $this->secretKey;
        $key = strtolower($key);
        $sign = md5($key);
        return strtoupper($sign);
    }

    /**
     * 发送HTTP GET请求
     * @param array $params 请求参数
     * @return array|false 返回JSON解码后的数组，失败返回false
     */
    private function sendRequest($params)
    {
        $url = $this->apiUrl . '?' . http_build_query($params);
        
        // 记录请求参数（隐藏敏感信息）
        $logParams = $params;
        if (isset($logParams['secretkey'])) {
            $logParams['secretkey'] = substr($logParams['secretkey'], 0, 10) . '...';
        }
        if (isset($logParams['sign'])) {
            $logParams['sign'] = substr($logParams['sign'], 0, 10) . '...';
        }
        Log::info('YesAgent API请求', [
            'action' => $params['action'] ?? 'unknown',
            'params' => $logParams,
            'url' => $url
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('YesAgent API请求失败', ['error' => $error, 'url' => $url]);
            return ['code' => -999, 'msg' => '请求失败: ' . $error, 'success' => false];
        }

        if ($httpCode !== 200) {
            Log::error('YesAgent API HTTP错误', ['http_code' => $httpCode, 'url' => $url, 'response' => $response]);
            return ['code' => -999, 'msg' => 'HTTP错误: ' . $httpCode, 'success' => false];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('YesAgent API响应解析失败', ['response' => $response, 'url' => $url, 'json_error' => json_last_error_msg()]);
            return ['code' => -999, 'msg' => '响应解析失败', 'success' => false];
        }
        
        // 记录响应结果
        Log::info('YesAgent API响应', [
            'action' => $params['action'] ?? 'unknown',
            'response' => $data
        ]);

        return $data;
    }

    /**
     * 1. 生成随机用户名
     * @param string $userName 你的代理名字
     * @return array
     */
    public function randomUserName($userName)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'RandomUserName',
            'userName' => $userName,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 2. 添加用户
     * @param string $userName 用户名
     * @param string $passWd 玩家密码（长度<=17）
     * @param string $name 用户昵称
     * @param string $tel 电话
     * @param string $memo 备注
     * @return array
     */
    public function addUser($userName, $passWd, $name, $tel, $memo = '')
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'addUser',
            'agent' => $this->agentId,
            'userName' => $userName,
            'PassWd' => $passWd,
            'Name' => $name,
            'Tel' => $tel,
            'Memo' => $memo,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 3. 编辑用户
     * @param string $userName 用户名
     * @param string $oldPassWd 玩家旧密码（长度<=17）
     * @param string $passWd 玩家新密码（长度<=17）
     * @param string $name 用户昵称
     * @param string $tel 电话
     * @param string $memo 备注
     * @return array
     */
    public function editUser($userName, $oldPassWd, $passWd, $name, $tel, $memo = '')
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'editUser',
            'userName' => $userName,
            'OldPassWd' => $oldPassWd,
            'PassWd' => $passWd,
            'Name' => $name,
            'Tel' => $tel,
            'Memo' => $memo,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 4. 玩家上下分
     * @param string $userName 用户名
     * @param float $scoreNum 上下分金额 >0 加分 <0 扣分
     * @param string $ordered 订单号（系统内唯一编号，如果重复就会返回-1）
     * @return array
     */
    public function setServerScore($userName, $scoreNum, $ordered)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'setServerScore',
            'userName' => $userName,
            'scoreNum' => $scoreNum,
            'ordered' => $ordered,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 5. 查询用户信息（搜索）
     * @param string $userName 用户名
     * @return array
     */
    public function getSearchUserInfo($userName)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'getSearchUserInfo',
            'userName' => $userName,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 6. 获取用户信息
     * @param string $userName 用户名
     * @return array
     */
    public function getUserInfo($userName)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'getUserInfo',
            'userName' => $userName,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 7. 用户分数记录
     * @param string $userName 用户名
     * @param string $sDate 起始日期 格式：2021-01-15 00:00:00
     * @param string $eDate 结束日期 格式：2021-01-15 23:59:59
     * @param int $pageIndex 页码，默认第一页
     * @return array
     */
    public function userScoreLog($userName, $sDate, $eDate, $pageIndex = 1)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'UserscoreLog',
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
            'pageIndex' => $pageIndex,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 8. 游戏记录
     * @param string $userName 用户名
     * @param string $sDate 起始日期 格式：2021-01-15 00:00:00
     * @param string $eDate 结束日期 格式：2021-01-15 23:59:59
     * @param int $pageIndex 页码，默认第一页
     * @return array
     */
    public function gameLog($userName, $sDate, $eDate, $pageIndex = 1)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'GameLog',
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
            'pageIndex' => $pageIndex,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 9. 禁用/启用用户
     * @param string $userName 用户名
     * @return array
     */
    public function disable($userName)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'disable',
            'userName' => $userName,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 10. 代理总报表
     * @param string $userName 用户名
     * @param string $sDate 起始日期 格式：2021-01-15
     * @param string $eDate 结束日期 格式：2021-01-15
     * @return array
     */
    public function agentTotalReport($userName, $sDate, $eDate)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'AgentTotalReport',
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 11. 玩家总报表
     * @param string $userName 用户名
     * @param string $sDate 起始日期 格式：2021-01-15
     * @param string $eDate 结束日期 格式：2021-01-15
     * @return array
     */
    public function playerTotalReport($userName, $sDate, $eDate)
    {
        $time = time();
        $sign = $this->generateSign($userName, $time);

        $params = [
            'action' => 'PlayerTotalReport',
            'userName' => $userName,
            'sDate' => $sDate,
            'eDate' => $eDate,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 12. 获取订单信息
     * @param string $orderId 订单ID
     * @return array
     */
    public function getOrder($orderId)
    {
        $time = time();
        $sign = $this->generateSignSpecial($time); // 使用特殊签名方式

        $params = [
            'action' => 'getOrder',
            'orderid' => $orderId,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }

    /**
     * 13. 踢出用户
     * @param string $userName 用户名
     * @return array
     */
    public function kick($userName)
    {
        $time = time();
        $sign = $this->generateSignSpecial($time); // 使用特殊签名方式

        $params = [
            'action' => 'kick',
            'userName' => $userName,
            'time' => $time,
            'authcode' => $this->authCode,
            'sign' => $sign,
        ];

        return $this->sendRequest($params);
    }
}

