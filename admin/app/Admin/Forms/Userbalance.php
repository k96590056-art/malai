<?php

namespace App\Admin\Forms;

use App\Models\TransferLog;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Widgets\Form;
use App\Models\Users;
use App\Models\UserOperateLog;
use App\Services\Lib;
use App\Models\Recharge;
use App\Models\Withdraw;
class Userbalance extends Form implements LazyRenderable
{
    use LazyWidget; // 使用异步加载功能
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $id = $this->payload['id'] ?? null;

        $balance= $input['balance'] ?? 0;
        if (! $id) {
            return $this->response()->error('参数错误');
        }

        if (!is_numeric($balance)) {
            return $this->response()->error('金额输入错误');
        }

        // 使用数据库事务确保数据一致性
        return \DB::transaction(function () use ($id, $balance, $input) {
            $user = Users::query()->find($id);
            if (! $user) {
                return $this->response()->error('用户不存在');
            }

            /*if($user->balance+$balance<0){
                return $this->response()->error('账户余额不足，无法完成扣除操作');
            }*/

            $before_balance = $user->balance;
            $after_balance = $user->balance + $balance;

            $arr = [
                'order_no' => time().rand(1000,9999),
                'api_type' => 'web',
                'user_id' => $user->id,
                'transfer_type' => ($balance<0) ? 4 : 3 ,
                'money' => $balance,
                'cash_fee' => 0,
                'real_money' => abs($balance),
                'before_money' => $before_balance,
                'after_money' => $after_balance,
                'state' => 1,
                'remark' => $input['balance_source']
            ];
            
            // 创建转账记录
            TransferLog::create($arr);
            
            // 更新用户余额
            $user->balance = $after_balance;
            $user->save();
            if($balance > 0){
                $Recharge = [
                    'order_no' => time().rand(1000,9999),
                    'out_trade_no' => time().rand(1000,9999),
                    'user_id' => $user->id,
                    'amount' => $balance,
                    'cash_fee' => 0,
                    'real_money' => $balance,
                    'pay_way' => 66,
                    'state' => 2,
                    'info' => '客服代充'
                ];
                Recharge::create($Recharge);
            }else{
                $Withdraw = [
                    'order_no' => time().rand(1000,9999),
                    'type' => 67,
                    'card_id' => 0,
                    'user_id' => $user->id,
                    'amount' => abs($balance),
                    'cash_fee' => 0,
                    'real_money' => abs($balance),
                    'state' => 2,
                    'info' => '客服代扣'
                ];
                Withdraw::create($Withdraw);                
            }
            
            // 简化IP地址查询，设置超时避免阻塞
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $ip_address = $this->getIpAddressFast($ip);
            
            UserOperateLog::insertLog(
                $user->id, 
                7, 
                $_SERVER['HTTP_USER_AGENT'] ?? '', 
                $ip, 
                $ip_address, 
                '管理员调整【' . $user->username . '】账户余额，调整金额数'.$balance.'，调整前金额'.$before_balance.'，调整后金额'.$after_balance
            );

            return $this->response()->success('账户余额调整成功')->refresh();
        });
    }

    /**
     * 快速获取IP地址信息，设置超时避免阻塞
     */
    private function getIpAddressFast($ip)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('https://67ip.cn/check?ip=%s&token=%s', $ip, '53319c68fdda40a8b905d032bac04f45'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 设置3秒超时
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 设置2秒连接超时
            
            $output = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code == 200 && $output) {
                $res = json_decode($output, true);
                if ($res && isset($res['code']) && $res['code'] == 200) {
                    return $res['data']['country'] . $res['data']['province'] . $res['data']['city'];
                }
            }
        } catch (\Exception $e) {
            \Log::error('IP地址查询失败: ' . $e->getMessage());
        }
        
        return '未知地区';
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        //$this->confirm('您确定要调整余额吗', 'content');
        $this->text('balance','调整金额')->rules('required')->default(0.00)->help('输入调整金额，整数为增加，负数为扣除');
        $this->text('balance_source','资金来源');
    }
}
