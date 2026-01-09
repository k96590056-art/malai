<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DbzhenrenService;
use Illuminate\Http\Request;

class DbzhenrenController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new DbzhenrenService();
    }

    /**
     * 单个会员余额查询
     */
    public function getBalance()
    {
        $result = $this->service->getBalance();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 批量会员余额查询
     */
    public function getBatchBalance(Request $request)
    {
        $result = $this->service->getBatchBalance();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 投注确认
     */
    public function betConfirm(Request $request)
    {
        $result = $this->service->betConfirm();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 投注取消
     */
    public function betCancel(Request $request)
    {
        $result = $this->service->betCancel();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 游戏派奖
     */
    public function gamePayout(Request $request)
    {
        $result = $this->service->gamePayout();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 活动派奖
     */
    public function activityPayout(Request $request)
    {
        $result = $this->service->activityPayout();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 玩家投注
     */
    public function playerBetting(Request $request)
    {
        $result = $this->service->playerBetting();
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * 活动返点
     */
    public function activityRebate(Request $request)
    {
        $result = $this->service->activityRebate();
        return response($result, 200)->header('Content-Type', 'application/json');
    }
}
