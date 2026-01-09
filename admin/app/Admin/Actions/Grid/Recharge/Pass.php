<?php

namespace App\Admin\Actions\Grid\Recharge;


use App\Models\SystemConfig;
use App\Models\Users;
use App\Models\UserVip;
use App\Models\Activity;
use App\Models\ActivityApply;
use App\Models\UserOperateLog;
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

class Pass extends RowAction
{
    /**
     * @return string
     */
	protected $title = '通过';

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
        $model = Recharge::find($id);
        $user = User::find($model->user_id);
        $user->balance += $model->amount;
        $user->paysum += $model->amount;
        $user->save();
        $model->state = 2;
        $model->save();
        $ip = $request->ip();
            $res = Lib::getIpAddress($ip);
            $res = json_decode($res, true);
            $ip_address = '';
            if ($res['code'] == 200) {
                $ip_address = $res['data']['country'] . $res['data']['province'] . $res['data']['city'];
            }
 UserOperateLog::insertLog($user->id, 7, $_SERVER['HTTP_USER_AGENT'], $ip, $ip_address, '管理员审核【' . $user->username . '】充值通过'.'充值金额'.$model->real_money);
/*        $Gamereport = new GamereportService();
        $data['uid'] = $user->id;
        $data['pid'] = $user->pid;
        $data['isagent'] = $user->isagent;
        $data['totalrechange'] =  $model->real_money;
        $Gamereport->add($data);*/
        self::sendmoney($user,$model->amount);
        //self::checkredbao($user,$model->real_money);
        self::upuserlevel($model->user_id);  //会员升级
        return $this->response()->success('审核成功')->refresh();
    }

    /**
     * 送金额
     * @return string|array|void
     */
    public function sendmoney($user,$money)
    {

        \Illuminate\Support\Facades\Log::info("充值送金额");
        $recharge_fee = SystemConfig::getValue("recharge_fee");
        if($recharge_fee) {
            $amount = $money * $recharge_fee /100;
            if($amount) {
                $user = User::find($user->id);
                $user->balance += $amount;
                $user->save();
                $arr['order_no'] = $user->id.time().rand(10000,90000);
                $arr['out_trade_no'] = $user->id.time().rand(10000,90000);
                $arr['user_id'] = $user->id;
                $arr['amount'] = $amount;
                $arr['cash_fee'] = 0;
                $arr['real_money'] =$amount;
                $arr['pay_way'] = 10;
                $arr['info'] = '充值送金额';
                $arr['state'] = 2;
                Recharge::create($arr);
            }
        }
    }


    /**
     * 发红包
     * @return string|array|void
     */
    public function checkredbao($user,$money)
    {
        if(Activity::where('user_id',$user->id)->where('activity_id',1)>count()){
            $redblist=RedEnvelopes::where(array('status'=>1))->get();
            foreach ($redblist as $val){
                if($val->day_flow<$money && $val->flow_money>$money){
                    $count = $this->getUserRedpacketNum($user,$val->id);
                    if($count<$val->recharge){ //红包数小于
                        $arr['uid'] = $user->id;
                        $arr['redpacketid'] = $val->id;
                        $arr['redpacketfee'] = $val->money;
                        $arr['money'] = $money;
                        $arr['redpacketmoney'] = $money * $val->money / 100 ;
                        $arr['status'] = 0;
                        Userredpacket::create($arr);

                    }
                }
            }
        }
    }

    public function getUserRedpacketNum($user,$redpacketid)
    {
        return Userredpacket::where(array('uid'=>$user->id,'redpacketid'=>$redpacketid))->count();
    }
    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		return ['确定审核通过', ''];
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

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }

    public function upuserlevel($uid){

        $userinfo = Users::find($uid);
        // $uservip = UserVip::where("status",1)->orderBy("id","desc")->get();
        $uservip = UserVip::where('status',1)->where('recharge','<=',$userinfo->paysum)->where('flow','<=',$userinfo->totalgame)->orderBy('id','desc')->first();
        $userinfo->vip = $uservip->id;
        $userinfo->save();
        // dd($uservip);
        // foreach ($uservip as $val){
        //     if($userinfo->paysum>=$val->recharge && $userinfo->totalgame>=$val->flow && $userinfo->vip>$val->id){
        //         $userinfo->vip = $val->id;
        //         $userinfo->save();
        //         break;
        //     }
        // }
    }
}
