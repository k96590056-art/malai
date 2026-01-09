<?php

namespace App\Console\Commands;

use App\Models\GameRecord;
use App\Services\TgService;
use Illuminate\Console\Command;
use App\User;
use Cache;
use App\Models\SystemConfig;
use App\Models\TransferLog;

class AllAgentFanyong extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AllAgentFanyong';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '全部代理返水';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 检查是否已经在执行返佣
        if (Cache::has('all_agent_fanyong_running')) {
            $this->info('返佣正在执行中，跳过本次执行');
            return;
        }
        
        $res = Cache::pull('all_agent_fanyong');
        if ($res && $res == 1) {
            // 设置执行状态标记
            Cache::put('all_agent_fanyong_running', 1, 300); // 5分钟超时
            
            try {
                $this->info('开始执行代理返佣...');
                
                // 按层级顺序处理代理：先处理顶级代理，再处理下级代理
                $processedCount = 0;
                $settlementday = intval(SystemConfig::getValue('settlement'));
                $diffday = strtotime(date('Y-m-d'))-$settlementday*60*60*24;
                
                // 获取所有代理，按层级排序（顶级代理优先）
                $allAgents = User::where('isagent', 1)
                    ->orderBy('pid', 'asc') // 顶级代理（pid=0）优先
                    ->orderBy('id', 'asc')
                    ->get();
                
                foreach ($allAgents as $agent) {
                    // 检查是否已经返佣过
                    if ($agent->settlementday >= strtotime(date('Y-m-d'))) {
                        $this->info("代理 {$agent->username} 今日已返佣，跳过");
                        continue;
                    }
                    
                    $money = 0;
                    $transfermoney = TransferLog::where("state", 2)
                        ->where('user_id', $agent->id)
                        ->where('transfer_type', 20)
                        ->sum('money');
                    
                    // 获取所有下级代理（包括多层级）
                    $child = User::getChild($agent->id);
                    $list = User::whereIn('id', $child)->get();
                    $totalfanhui = 0;
                    $totalredpacketSum = 0;
                    $totalRechargeredpacketSum = 0;
                    
                    foreach ($list as $childAgent) {
                        // 反水
                        $totalfanhui += User::totalfanhui(
                            $childAgent->id, 
                            date('Y-m-d', $diffday) . ' 00:00:00', 
                            date('Y-m-d', time()) . ' 23:59:59'
                        );
                        // 红包
                        $totalredpacketSum += User::redpacketSum(
                            $childAgent->id, 
                            date('Y-m-d', $diffday) . ' 00:00:00', 
                            date('Y-m-d', time()) . ' 23:59:59'
                        );
                        // 充值送红包
                        $totalRechargeredpacketSum += User::RechargeredpacketSum(
                            $childAgent->id, 
                            date('Y-m-d', $diffday) . ' 00:00:00', 
                            date('Y-m-d', time()) . ' 23:59:59'
                        );
                    }
                    
                    // 计算净佣金（总佣金 - 分配给下级的费用）
                    $money = $transfermoney - $totalfanhui - $totalredpacketSum - $totalRechargeredpacketSum;
                    
                    if ($money > 0) {
                        $agent->balance = $agent->balance + $money;
                        $agent->save();
                        
                        // 更新佣金记录状态为已结算
                        TransferLog::where("state", 2)
                            ->where('user_id', $agent->id)
                            ->where('transfer_type', 20)
                            ->update(['state' => 1]);
                    }
                    
                    // 更新结算日期
                    $agent->settlementday = strtotime(date('Y-m-d'));
                    $agent->save();
                    
                    $processedCount++;
                    $this->info("代理 {$agent->username} 返佣完成，金额: {$money}");
                }
                
                $this->info("代理返佣执行完成，处理了 {$processedCount} 个代理");
                
            } catch (\Exception $e) {
                $this->error('执行代理返佣时发生错误: ' . $e->getMessage());
            } finally {
                // 清除执行状态标记
                Cache::forget('all_agent_fanyong_running');
            }
        }
    }
}
