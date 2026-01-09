<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TgService;
use App\Services\YesAgent;
use App\Services\DbService;
use App\Models\GameList;
use App\Models\GameRecord;
use App\Models\User;
use App\Models\SystemConfig;
use App\Models\AgentInterface;
use Illuminate\Support\Facades\Log;

class SyncGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:game-records {--type= : 同步类型，db 或 yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步游戏记录（支持 Db 和 Yes 两种类型）';

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
        $type = $this->option('type');
        
        if (empty($type)) {
            $this->error('请指定同步类型：--type=db 或 --type=yes');
            return 1;
        }

        $type = strtolower($type);
        
        if (!in_array($type, ['db', 'yes'])) {
            $this->error('同步类型只能是 db 或 yes');
            return 1;
        }

        $this->info("开始同步 {$type} 游戏记录...");

        try {
            if ($type === 'yes') {
                $this->syncYesGameRecords();
            } elseif ($type === 'db') {
                $this->syncDbGameRecords();
            }
            
            $this->info("同步完成！");
            return 0;
        } catch (\Exception $e) {
            $this->error("同步失败：" . $e->getMessage());
            Log::error('同步游戏记录失败', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * 同步 Yes 游戏记录
     */
    private function syncYesGameRecords()
    {
        // 获取 YesAgent 接口信息
        $yesInterface = AgentInterface::where('code', 'YesAgent')->first();
        
        if (!$yesInterface) {
            $this->error('未找到 YesAgent 接口配置');
            return;
        }

        // 获取所有使用 YesAgent 接口的代理
        $agents = User::where('isagent', 1)
            ->where('status', 1)
            ->where('agent_api_id', $yesInterface->id)
            ->whereNotNull('autocode')
            ->whereNotNull('secretkey')
            ->get();

        if ($agents->isEmpty()) {
            $this->warn('未找到使用 YesAgent 接口的代理');
            return;
        }

        $this->info("找到 {$agents->count()} 个 YesAgent 代理，开始同步...");

        // 计算时间范围（默认同步最近7天的记录）
        $endDate = date('Y-m-d H:i:s');
        $startDate = date('Y-m-d 00:00:00', strtotime('-7 days'));

        $totalRecords = 0;
        $totalSuccessCount = 0;
        $totalFailCount = 0;

        // 遍历每个代理
        foreach ($agents as $agent) {
            $this->info("正在同步代理 [{$agent->username}] 的游戏记录...");

            // 使用代理的 autocode 和 secretkey 初始化 YesAgent
            $yesAgent = new YesAgent($agent->username, $agent->autocode, $agent->secretkey);

            // 获取该代理下的所有用户（包括直接下线和所有下级）
            $userIds = $this->getAgentAllUserIds($agent->id);
            $users = User::whereIn('id', $userIds)->where('status', 1)->get();

            if ($users->isEmpty()) {
                $this->warn("代理 [{$agent->username}] 下没有用户");
                continue;
            }

            $this->info("代理 [{$agent->username}] 下有 {$users->count()} 个用户");

            $bar = $this->output->createProgressBar($users->count());
            $bar->start();

            $agentSuccessCount = 0;
            $agentFailCount = 0;

            foreach ($users as $user) {
                try {
                    $pageIndex = 1;
                    $hasMore = true;

                    while ($hasMore) {
                        // 调用 YesAgent 的 gameLog 方法获取游戏记录
                        $result = $yesAgent->gameLog($user->username, $startDate, $endDate, $pageIndex);

                        if (isset($result['code']) && $result['code'] == 0 && isset($result['data'])) {
                            $records = $result['data'];
                            
                            if (empty($records) || !is_array($records)) {
                                $hasMore = false;
                                break;
                            }

                            foreach ($records as $record) {
                                // 检查记录是否已存在
                                $betId = $record['betId'] ?? $record['bet_id'] ?? '';
                                if (empty($betId)) {
                                    continue;
                                }

                                $exists = GameRecord::where('bet_id', $betId)
                                    ->where('user_id', $user->id)
                                    ->exists();

                                if (!$exists) {
                                    // 映射 Yes 接口返回的字段到数据库字段
                                    $gameRecord = [
                                        'user_id' => $user->id,
                                        'username' => $user->username,
                                        'bet_id' => $betId,
                                        'bet_time' => $record['betTime'] ?? $record['bet_time'] ?? now(),
                                        'platform_type' => $record['platformType'] ?? $record['platform_type'] ?? 'yes',
                                        'game_type' => $record['gameType'] ?? $record['game_type'] ?? '',
                                        'game_code' => $record['gameCode'] ?? $record['game_code'] ?? '',
                                        'bet_amount' => $record['betAmount'] ?? $record['bet_amount'] ?? 0,
                                        'valid_amount' => $record['validAmount'] ?? $record['valid_amount'] ?? 0,
                                        'win_loss' => $record['winLoss'] ?? $record['win_loss'] ?? 0,
                                        'status' => $this->mapYesStatus($record['status'] ?? $record['Status'] ?? 0),
                                        'is_back' => 0,
                                    ];

                                    GameRecord::create($gameRecord);
                                    $totalRecords++;
                                    $agentSuccessCount++;
                                    $totalSuccessCount++;
                                }
                            }

                            // 检查是否还有更多页
                            $totalPages = $result['totalPages'] ?? $result['total_pages'] ?? 1;
                            if ($pageIndex >= $totalPages) {
                                $hasMore = false;
                            } else {
                                $pageIndex++;
                            }
                        } else {
                            $hasMore = false;
                            if (isset($result['msg']) || isset($result['message'])) {
                                Log::warning('Yes 获取游戏记录失败', [
                                    'agent' => $agent->username,
                                    'username' => $user->username,
                                    'message' => $result['msg'] ?? $result['message'] ?? '未知错误'
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $agentFailCount++;
                    $totalFailCount++;
                    Log::error('同步 Yes 游戏记录失败', [
                        'agent' => $agent->username,
                        'username' => $user->username,
                        'error' => $e->getMessage()
                    ]);
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("代理 [{$agent->username}] 同步完成：成功 {$agentSuccessCount} 条，失败 {$agentFailCount} 条");
        }

        $this->newLine();
        $this->info("所有代理同步完成：成功 {$totalSuccessCount} 条，失败 {$totalFailCount} 条，总计 {$totalRecords} 条新记录");
    }

    /**
     * 获取代理下的所有用户ID（递归获取所有下级）
     * @param int $agentId 代理ID
     * @return array 用户ID数组
     */
    private function getAgentAllUserIds($agentId)
    {
        $userIds = [];
        $this->getUserIdsRecursive($agentId, $userIds);
        return $userIds;
    }

    /**
     * 递归获取用户ID（只获取会员，跳过代理）
     * @param int $parentId 父级ID
     * @param array &$userIds 用户ID数组（引用传递）
     */
    private function getUserIdsRecursive($parentId, &$userIds)
    {
        $children = User::where('pid', $parentId)
            ->where('status', 1)
            ->get();

        foreach ($children as $child) {
            // 如果是会员（isagent=0），添加到结果数组
            if ($child->isagent == 0) {
                $userIds[] = $child->id;
            }
            // 无论是否是代理，都递归获取其下级（因为代理下可能还有会员）
            $this->getUserIdsRecursive($child->id, $userIds);
        }
    }

    /**
     * 同步 Db 游戏记录
     */
    private function syncDbGameRecords()
    {
        $dbService = new DbService();

        // 获取所有用户
        $users = User::where('status', 1)->get();
        $totalUsers = $users->count();
        $this->info("找到 {$totalUsers} 个用户，开始同步...");

        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();

        $totalRecords = 0;
        $successCount = 0;
        $failCount = 0;

        // 计算时间范围（默认同步最近7天的记录）
        $endDate = date('Y-m-d H:i:s');
        $startDate = date('Y-m-d 00:00:00', strtotime('-7 days'));

        foreach ($users as $user) {
            try {
                // 调用 DbService 的 getGameRecords 方法获取游戏记录
                $pageNum = 0;
                $hasMore = true;

                while ($hasMore) {
                    $result = $dbService->getGameRecords($user->username, $startDate, $endDate, $pageNum, 100);

                    if (isset($result['code']) && $result['code'] == 0 && isset($result['data'])) {
                        // Db 接口返回的数据可能在 data.list 中
                        $records = $result['data']['list'] ?? $result['data'];
                        
                        if (empty($records) || !is_array($records)) {
                            $hasMore = false;
                            break;
                        }

                        foreach ($records as $record) {
                            // 检查记录是否已存在
                            $betId = $record['betId'] ?? $record['bet_id'] ?? $record['orderId'] ?? $record['order_id'] ?? '';
                            if (empty($betId)) {
                                continue;
                            }

                            $exists = GameRecord::where('bet_id', $betId)
                                ->where('user_id', $user->id)
                                ->exists();

                            if (!$exists) {
                                // 映射 Db 接口返回的字段到数据库字段
                                $gameRecord = [
                                    'user_id' => $user->id,
                                    'username' => $user->username,
                                    'bet_id' => $betId,
                                    'bet_time' => $record['betTime'] ?? $record['bet_time'] ?? $record['createTime'] ?? $record['create_time'] ?? now(),
                                    'platform_type' => $record['platformType'] ?? $record['platform_type'] ?? $record['venueCode'] ?? 'db',
                                    'game_type' => $record['gameType'] ?? $record['game_type'] ?? '',
                                    'game_code' => $record['gameCode'] ?? $record['game_code'] ?? $record['gameId'] ?? '',
                                    'bet_amount' => $record['betAmount'] ?? $record['bet_amount'] ?? 0,
                                    'valid_amount' => $record['validAmount'] ?? $record['valid_amount'] ?? 0,
                                    'win_loss' => $record['winLoss'] ?? $record['win_loss'] ?? $record['profit'] ?? 0,
                                    'status' => $this->mapDbStatus($record['status'] ?? $record['Status'] ?? 0),
                                    'is_back' => 0,
                                ];

                                GameRecord::create($gameRecord);
                                $totalRecords++;
                                $successCount++;
                            }
                        }

                        // 检查是否还有更多页
                        $totalRecord = $result['data']['totalRecord'] ?? $result['data']['total_record'] ?? 0;
                        $currentCount = ($pageNum + 1) * 100;
                        if ($currentCount >= $totalRecord || count($records) < 100) {
                            $hasMore = false;
                        } else {
                            $pageNum++;
                        }
                    } else {
                        $hasMore = false;
                        if (isset($result['message']) || isset($result['Message'])) {
                            Log::warning('Db 获取游戏记录失败', [
                                'username' => $user->username,
                                'message' => $result['message'] ?? $result['Message'] ?? '未知错误'
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                $failCount++;
                Log::error('同步 Db 游戏记录失败', [
                    'username' => $user->username,
                    'error' => $e->getMessage()
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("同步完成：成功 {$successCount} 条，失败 {$failCount} 条，总计 {$totalRecords} 条新记录");
    }

    /**
     * 映射 Yes 状态到数据库状态
     * @param mixed $status
     * @return int
     */
    private function mapYesStatus($status)
    {
        // Yes 接口的状态映射
        // 1=已结算, 2=未结算, 0=无效注单
        if (is_string($status)) {
            $status = strtolower($status);
            if ($status === 'settled' || $status === '已结算') {
                return 1;
            } elseif ($status === 'unsettled' || $status === '未结算') {
                return 2;
            } else {
                return 0;
            }
        }
        
        return (int)$status;
    }

    /**
     * 映射 Db 状态到数据库状态
     * @param mixed $status
     * @return int
     */
    private function mapDbStatus($status)
    {
        // Db 接口的状态映射
        // 1=已结算, 2=未结算, 0=无效注单
        if (is_string($status)) {
            $status = strtolower($status);
            if ($status === 'settled' || $status === '已结算') {
                return 1;
            } elseif ($status === 'unsettled' || $status === '未结算') {
                return 2;
            } else {
                return 0;
            }
        }
        
        return (int)$status;
    }
}
