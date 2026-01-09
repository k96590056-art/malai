<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Recharge;
use App\Models\SystemConfig;
use App\User;
use Carbon\Carbon;

/**
 * TRON USDT充值服务类
 */
class TronUsdtService
{
    private function buildHeaders(): array
    {
        // 可选启用 API Key（用于提高限频）
        $useKey = (int) SystemConfig::getValue('tron_api_key_enabled', 0) === 1;
        $key = trim((string) SystemConfig::getValue('tron_api_key'));
        $headers = [];
        if ($useKey && $key !== '') {
            $headers[] = 'TRON-PRO-API-KEY: ' . $key;
        }
        return $headers;
    }

    /**
     * 发起 GET 请求（cURL）
     */
    private function httpGet(string $url, array $headers = [], int $timeoutSec = 20)
    {
        $makeHeaders = function(array $headers) {
            $list = [];
            foreach ($headers as $h) {
                if ($h) $list[] = $h;
            }
            // 统一追加UA，便于某些网关放行
            $list[] = 'User-Agent: TronUSDT-Client/1.0';
            $list[] = 'Accept: application/json';
            return $list;
        };

        $doRequest = function(bool $skipSSL) use ($url, $headers, $timeoutSec, $makeHeaders) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeoutSec);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutSec);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $makeHeaders($headers));
            // HTTPS SSL 验证（根据 skipSSL 控制）
            if (stripos($url, 'https://') === 0) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $skipSSL ? 0 : 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $skipSSL ? 0 : 2);
            }
            $resp = curl_exec($ch);
            $errno = curl_errno($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errMsg = curl_error($ch);
            curl_close($ch);
            return [$resp, $errno, $status, $errMsg];
        };

        // 第一次：正常校验证书
        [$resp, $errno, $status, $errMsg] = $doRequest(false);
        if ($errno === 60) {
            // SSL 证书校验失败，自动降级重试（仅本次进程），并记录告警日志
            Log::warning('cURL SSL verify failed (errno 60), retry without verify', [ 'url' => $url ]);
            [$resp, $errno, $status, $errMsg] = $doRequest(true);
        }

        if ($errno) {
            throw new \Exception('HTTP请求失败: ' . $errno . ($errMsg ? (' - ' . $errMsg) : ''));
        }
        if ($status < 200 || $status >= 300) {
            throw new \Exception('HTTP响应码异常: ' . $status);
        }
        return $resp;
    }

    /**
     * 生成USDT充值信息
     */
    public function generateRechargeInfo($amount, $userId)
    {
        try {
            $usdtRate = SystemConfig::getValue('tron_exchange_rate') ?: 7;
            $usdtAmount = round($amount / $usdtRate, 3);
            $randomDecimal = mt_rand(1, 999) / 1000; // 0.001 ~ 0.999
            // 统一按三位小数四舍五入，避免浮点误差导致前后不一致
            $finalUsdtAmount = round($usdtAmount + $randomDecimal, 3);
            $tronAddress = SystemConfig::getValue('tron_usdt_address');
            if (empty($tronAddress)) {
                throw new \Exception('TRON收款地址未配置');
            }
            $outTradeNo = 'TRON_' . time() . $userId . mt_rand(1000, 9999);
            return [
                'success' => true,
                'data' => [
                    'out_trade_no' => $outTradeNo,
                    'usdt_amount' => $finalUsdtAmount,
                    'tron_address' => $tronAddress,
                    'exchange_rate' => $usdtRate,
                    'random_decimal' => number_format($randomDecimal, 3, '.', ''),
                    'original_amount' => $amount
                ]
            ];
        } catch (\Exception $e) {
            Log::error('生成TRON USDT充值信息失败:', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'user_id' => $userId
            ]);
            return [
                'success' => false,
                'message' => '生成充值信息失败: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 验证交易哈希
     */
    public function verifyTransaction($txHash, $outTradeNo)
    {
        try {
            $recharge = Recharge::where('out_trade_no', $outTradeNo)->first();
            if (!$recharge) return ['success' => false, 'message' => '订单不存在'];
            if ($recharge->state != 1) return ['success' => false, 'message' => '订单状态异常，无法验证'];
            if (!empty($recharge->tron_tx_hash)) return ['success' => false, 'message' => '订单已处理，不能重复提交哈希'];
            
            // 验证交易哈希格式（必须是64位十六进制字符串）
            if (!preg_match('/^[a-f0-9]{64}$/i', $txHash)) {
                return ['success' => false, 'message' => '交易哈希格式错误，请输入正确的TRON交易哈希'];
            }

            $apiUrl = SystemConfig::getValue('tron_api_url') ?: 'https://apilist.tronscanapi.com';
            $base = rtrim($apiUrl, '/');
            $headers = $this->buildHeaders();

            // 1) 交易信息
            $txJson = $this->httpGet($base . '/api/transaction-info?hash=' . urlencode($txHash), $headers);
            $txData = json_decode($txJson, true) ?: [];
            $blockNumber = null;
            if (isset($txData['blockNumber'])) {
                $blockNumber = (int)$txData['blockNumber'];
            } elseif (isset($txData['block'])) {
                $blockNumber = (int)$txData['block'];
            }

            // 2) 最新区块/确认数
            $latestJson = $this->httpGet($base . '/api/block?sort=-number&limit=1', $headers);
            $latestData = json_decode($latestJson, true) ?: [];
            $latestBlock = isset($latestData['data'][0]['number']) ? (int)$latestData['data'][0]['number'] : null;
            if ($blockNumber !== null && $latestBlock !== null) {
                $confirmations = max(0, $latestBlock - $blockNumber);
            } else {
                $confirmations = (int)($txData['confirmations'] ?? 0);
            }
            $requiredConfirmations = (int) SystemConfig::getValue('tron_confirmations', 12);
            if ($confirmations < $requiredConfirmations) {
                return ['success' => false, 'message' => "交易确认数不足，当前{$confirmations}个，需要{$requiredConfirmations}个"];
            }

            // 3) 事件校验：先用 events，失败或为空则回退用 trc20TransferInfo
            $event = $this->fetchUsdtTransferEventFromTronscan($txHash, $apiUrl);
            if (!$event) {
                // 回退使用 transaction-info 内的 trc20TransferInfo
                $event = $this->extractFromTrc20TransferInfo($txData);
            }
            if (!$event) return ['success' => false, 'message' => '未找到USDT转账事件'];

            $expectedAddress = SystemConfig::getValue('tron_usdt_address');
            $receivedTo = $event['to'] ?? '';
            if (strcasecmp($receivedTo, $expectedAddress) !== 0) return ['success' => false, 'message' => '收款地址不匹配'];

            $expectedAmount = $recharge->tron_usdt_amount ?? $recharge->real_money;
            $receivedAmount = isset($event['value']) ? ((float)$event['value'] / 1000000) : 0.0;
            if (abs($receivedAmount - (float)$expectedAmount) > 0.001) {
                return ['success' => false, 'message' => '充值金额不匹配，应当转入: ' . $expectedAmount . ' USDT'];
            }

            $this->processSuccessfulRecharge($recharge, $txHash, $receivedAmount, $confirmations);
            return ['success' => true, 'message' => '验证成功，充值已到账', 'data' => [ 'tx_hash' => $txHash, 'amount' => $receivedAmount, 'confirmations' => $confirmations ]];
        } catch (\Exception $e) {
            Log::error('验证TRON交易失败:', ['error' => $e->getMessage(), 'tx_hash' => $txHash, 'out_trade_no' => $outTradeNo]);
            return ['success' => false, 'message' => '验证失败: ' . $e->getMessage()];
        }
    }

    private function processSuccessfulRecharge($recharge, $txHash, $amount, $confirmations = null)
    {
        try {
            $recharge->update([
                'state' => 2,
                'info' => "TRON USDT充值成功，交易哈希: {$txHash}，实际到账: {$amount} USDT",
                'order_no' => 'TRON_' . time() . $recharge->id,
                'tron_tx_hash' => $txHash,
                'tron_confirmations' => $confirmations ?? $recharge->tron_confirmations,
                'tron_paid_at' => Carbon::now(),
            ]);
            $user = User::find($recharge->user_id);
            if ($user) {
                $user->balance += $recharge->amount;
                $user->save();
                Log::info('TRON USDT充值成功:', [
                    'user_id' => $user->id,
                    'amount' => $recharge->amount,
                    'usdt_amount' => $amount,
                    'tx_hash' => $txHash
                ]);
            }
        } catch (\Exception $e) {
            Log::error('处理TRON USDT充值失败:', [
                'error' => $e->getMessage(),
                'recharge_id' => $recharge->id,
                'tx_hash' => $txHash
            ]);
            throw $e;
        }
    }

    public function handleCallback($callbackData)
    {
        try {
            Log::info('收到TRON USDT充值回调:', $callbackData);
            $txHash = $callbackData['txid'] ?? $callbackData['tx_hash'] ?? '';
            $outTradeNo = $callbackData['out_trade_no'] ?? $callbackData['order_no'] ?? '';
            if (empty($txHash) || empty($outTradeNo)) {
                throw new \Exception('回调数据不完整');
            }
            $verify = $this->verifyTransaction($txHash, $outTradeNo);
            if ($verify['success'] ?? false) {
                return ['success' => true, 'message' => '回调处理成功', 'data' => $verify['data'] ?? []];
            }
            return ['success' => false, 'message' => $verify['message'] ?? '回调校验失败'];
        } catch (\Exception $e) {
            Log::error('处理TRON回调失败:', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData
            ]);
            return ['success' => false, 'message' => '回调处理失败: ' . $e->getMessage()];
        }
    }

    private function fetchUsdtTransferEventFromTronscan(string $txHash, string $apiUrl): ?array
    {
        $base = rtrim($apiUrl ?: 'https://apilist.tronscanapi.com', '/');
        $url = $base . '/api/contract/events?hash=' . urlencode($txHash) . '&limit=20&start=0';
        $json = null;
        try {
            $json = $this->httpGet($url, $this->buildHeaders());
        } catch (\Exception $e) {
            // 400/限流等异常直接回退，让上层用 trc20TransferInfo
            Log::warning('Tronscan events 请求失败，使用 trc20TransferInfo 回退', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
        $data = json_decode($json, true);
        if (!is_array($data)) return null;
        $usdtContract = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
        $events = $data['data'] ?? $data ?? [];
        foreach ($events as $evt) {
            $contract = $evt['contract'] ?? ($evt['contract_address'] ?? '');
            $eventName = $evt['name'] ?? ($evt['event_name'] ?? '');
            $result = $evt['result'] ?? $evt;
            $to = $result['to'] ?? ($result['to_address'] ?? '');
            $value = $result['value'] ?? ($result['amount'] ?? 0);
            if (strcasecmp($contract, $usdtContract) === 0 && strcasecmp($eventName, 'Transfer') === 0) {
                return [
                    'contract_address' => $contract,
                    'from' => $result['from'] ?? '',
                    'to' => $to,
                    'value' => (float)$value,
                ];
            }
        }
        return null;
    }

    private function extractFromTrc20TransferInfo(array $txData): ?array
    {
        // trc20TransferInfo 数组元素：to_address、contract_address、amount_str、decimals
        if (!isset($txData['trc20TransferInfo']) || !is_array($txData['trc20TransferInfo'])) {
            return null;
        }
        $usdtContract = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
        foreach ($txData['trc20TransferInfo'] as $info) {
            $contract = $info['contract_address'] ?? '';
            if (strcasecmp($contract, $usdtContract) !== 0) continue;
            $to = $info['to_address'] ?? '';
            $amountStr = $info['amount_str'] ?? '0';
            $value = (float)$amountStr; // 单位 1e6
            return [
                'contract_address' => $contract,
                'from' => $info['from_address'] ?? '',
                'to' => $to,
                'value' => $value,
            ];
        }
        return null;
    }




}
