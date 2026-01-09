<?php

namespace App\Admin\Actions\Grid\User;


use App\Models\SystemConfig;
use App\Models\Users;
use App\Models\TransferLog;
use App\Services\Lib;
use App\Services\GamereportService;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Recharge;
use App\Models\RedEnvelopes;
use App\Models\Userredpacket;
use App\User;

class Fanyong extends RowAction
{
    /**
     * @return string
     */
	protected $title = '立即返佣';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $id = $this->getKey();

        $today = date('N');
        if($today != '1'){
            return $this->response()->error('每周一可反上周佣金');
        }
        // 正确计算上周的开始时间（上周一）
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('-7 days'));
        // 正确计算上周的结束时间（上周日）
        $lastWeekEnd = date('Y-m-d 23:59:59', strtotime('-1 days'));
        $TransferLog = TransferLog::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->where('user_id',$id)->where('state',2)->where('transfer_type',999)->sum('yongjin');        
        if($TransferLog > 0){
            $Users = Users::find($id);
            $Users->balance = $Users->balance + $TransferLog;
            $Users->save();
            $TransferLog_update = TransferLog::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->where('user_id',$id)->where('state',2)->where('transfer_type',999)->update(['state' => 1]);            
        }
        return $this->response()->success('成功领取返佣： '.$TransferLog)->refresh();
        
        $money = 0;
        $settlementday = intval(SystemConfig::getValue('settlement'));
        $diffday = strtotime(date('Y-m-d'))-$settlementday*60*60*24;
        $val = User::where('isagent','=',1)->where('id','=',$id)->first();
        
        if ($val){
            // 检查是否已经返佣过
            if ($val->settlementday >= strtotime(date('Y-m-d'))) {
                return $this->response()->error('该代理今日已返佣，请勿重复操作');
            }
            
            $transfermoney = TransferLog::where("state",2)->where('user_id',$val->id)->where('transfer_type',20)->sum('money');

            $child = User::getChild($val->id);
            $list = User::whereIn('id',$child)->get();
            $totalfanhui = 0;
            $totalredpacketSum =0;
            $totalRechargeredpacketSum =0;
            foreach ($list as $k => $v) {
                //反水
                $totalfanhui += User::totalfanhui($v->id, date('Y-m-d', $diffday) . ' 00:00:00', date('Y-m-d', time()) . ' 23:59:59');
                //紅包
                $totalredpacketSum +=   User::redpacketSum($v->id, date('Y-m-d', $diffday) . ' 00:00:00', date('Y-m-d', time()) . ' 23:59:59');
                // 充值送红包
                $totalRechargeredpacketSum +=   User::RechargeredpacketSum($v->id, date('Y-m-d', $diffday) . ' 00:00:00', date('Y-m-d', time()) . ' 23:59:59');
            }
            $user = User::where('id',$val->id)->first();
            // $money =  $transfermoney -  $totalfanhui - $totalredpacketSum - $totalRechargeredpacketSum;
            $money = $transfermoney;
            // dd($money);
            if ($money>0) {

                $user->balance = $user->balance + $money;

                TransferLog::where("state",2)->where('user_id',$val->id)->where('transfer_type',20)->update(['state'=>1]);
            }
                $user->settlementday = strtotime(date('Y-m-d'));
                $user->save();
        }        
        

        return $this->response()->success('成功领取返佣'.$money)->refresh();
    }


    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		return ['确定立即返佣', ''];
	}

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }
}
