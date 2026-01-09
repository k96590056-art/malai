<?php
//decode by http://www.yunlu99.com/
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CodePay;
use App\Models\GameRecord;
use App\Models\PaySetting;
use App\Models\Usersmoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recharge;
use App\Models\SystemConfig;
use App\Models\TransferLog;
use App\Models\UserCard;
use App\Models\Withdraw;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Services\TgService;
use App\Models\Template;
use App\Services\Zgpay;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
class PayController extends Controller
{
    protected $path;

    protected $state = [1 => '待审核', 2 => '通过', 3 => '失败'];

    protected $showlang;
    public function __construct()
    {
        $lang = Cookie::get("userlang");
        $this->showlang = $lang;
        if($lang=="en"){
            $path = 'web.template.e_mb10';    
        }else{
            $path = Template::where('client_type',1)->where('state',2)->first();
            $path = $path ? 'web.template.'.$path->template_id : 'web';            
        }   
        $this->path = $path;
    }

    public function recharge()
    {
         $lang = $this->showlang;
        $cardlist = PaySetting::where('state',1)->get();
        // dd($cardlist);
        $wxinfo = CodePay::where('status',1)->where('id',3)->first();
        $usdtinfo = CodePay::where('status',1)->where('id',5)->first();
        $usdtinfo_erc = CodePay::where('status',1)->where('id',7)->first();
        $alipayinfo = CodePay::where('status',1)->where('id',4)->first();
        $list = Recharge::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
        $usdt_rate = SystemConfig::getValue('usdt_rate') ?? 0;
        foreach ($list as $k => $v) {
            $list[$k]['state'] = $this->state[$v->state];
            $list[$k]['type'] = ($v->pay_way==10) ? '充值赠送': '充值';
        }
        return view($this->path.'.member.recharge',compact('cardlist','wxinfo','alipayinfo','list','lang','usdtinfo','usdt_rate','usdtinfo_erc'));
    }

    public function rechargeDo(Request $request)
    {
        $data = $request->all();
        $min_recharge_money = SystemConfig::getValue('min_recharge_money');
        $max_recharge_money = SystemConfig::getValue('max_recharge_money');
        if (isset($min_recharge_money) && !empty($min_recharge_money)) {
            if ($data['amount'] < $min_recharge_money) {
                return $this->returnMsg(212,[],'单次充值最低金额：'.$min_recharge_money);
            }
        }
        if (isset($max_recharge_money) && !empty($max_recharge_money)) {
            if ($data['amount'] > $max_recharge_money) {
                return $this->returnMsg(213,[],'单次充值最高金额：'.$max_recharge_money);
            }
        }
        $out_trade_no = time().Auth::id().rand(1000,9999);
        $data['out_trade_no'] = $out_trade_no;
        $data['user_id'] = Auth::id();
        // $data['usdt_rate'] = $data['pay_way'] == 5 ? SystemConfig::getValue('usdt_rate') : 0;
        switch ($data['pay_way']) {
            case 1: //提交后台审核
                $data['cash_fee'] = 0;
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500);
                break;
            case 3: //提交后台审核  alipay
                $data['cash_fee'] = 0;
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500);
                break;
            case 4: //提交后台审核  wxpay
                $data['cash_fee'] = 0;
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500);
                break;
            case 5: //提交后台审核  USDT-TRC20
                $data['cash_fee'] = 0;
                $data['usdt_rate'] = SystemConfig::getValue('usdt_rate');
                $data['real_money'] = sprintf('%.2f',$data['amount'] / $data['usdt_rate']);
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500);
                break;
            case 6: //提交后台审核  USDT-ERC20
                $data['cash_fee'] = 0;
                $data['usdt_rate'] = SystemConfig::getValue('usdt_rate');
                $data['real_money'] = sprintf('%.2f',$data['amount'] / $data['usdt_rate']);
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500);

            default:
                # code...
                break;
        }
    }

    private function fourWaypayCurl($out_trade_no,$total_fee,$return_url,$notify_url,$payType="wechat",$CreateIp="")
    {
        $url = 'http://api.fubas.xyz:8013/api/startOrder';
        $merchant_id = 'tgdemo';
        //商户密钥
        $api_secret = 'e9afed057f49f46fc7518dd84135d73a';

        $data = array(
            'merchantNum'=> $merchant_id,
            'orderNo'=> $out_trade_no,
            'amount'=> $total_fee,
            'notifyUrl'=>$notify_url,
            'payType'=> $payType,
            'returnUrl'=> $return_url,
            'ip'=> $CreateIp,
        );

        $data['Sign'] = md5($data['merchantNum'].$data['orderNo'].$data['amount'].$data['notifyUrl'].$api_secret);
        $headers = array(
           // "Content-Type: application/json",
            "lang: zh-cn",
        );
        \Illuminate\Support\Facades\Log::info("充值回调结果");
        \Illuminate\Support\Facades\Log::info($data);
        \Illuminate\Support\Facades\Log::info( json_encode($data));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Execute the request
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,true);
        \Illuminate\Support\Facades\Log::info("充值回调结果");
        \Illuminate\Support\Facades\Log::info($result);

        return $result;
    }

    private function zgpayCurl($out_trade_no,$total_fee,$return_url,$notify_url,$order_description="充值",$time_out=300,$param="")
    {
    	$url = 'http://zgpay.cc/api/createOrder';
        $ch = curl_init($url);

        $merchant_id = SystemConfig::where('key','merchant_id')->value('value') ?? '';
        $api_secret = SystemConfig::where('key','zgp_secret')->value('value') ?? '';

		$sign = "";
		if ($merchant_id != '') {
		    $sign .= $merchant_id;
		}
		if ($notify_url != '') {
		    $sign .= $notify_url;
		}
		if ($order_description != '') {
		    $sign .= $order_description;
		}
		if ($out_trade_no != '') {
		    $sign .= $out_trade_no;
		}
		if ($param != '') {
		    $sign .= $param;
		}
		if ($return_url != '') {
		    $sign .= $return_url;
		}
		if ($time_out != '') {
		    $sign .= $time_out;
		}
		if ($total_fee != '') {
		    $sign .= $total_fee;
		}
		$sign = md5($sign.$api_secret);

		$data = array(
		  'merchant_id'=> $merchant_id,
		  'notify_url'=>$notify_url,
		  'order_description'=>$order_description,
		  'time_out'=> $time_out,
		  'out_trade_no'=> $out_trade_no,
		  'param' => $param,
		  'return_url'=> $return_url,
		  'time_out'=> $time_out,
		  'total_fee'=> $total_fee,
		  'sign'=> $sign,
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//Execute the request
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result,true);
		return $result;
    }

    /**
     * 异步回调
     *
     * @param Request $request
     * @return void
     */
    public function notify()
    {
        $content = file_get_contents("php://input");
        $data = json_decode($content, true);

        $api_secret = SystemConfig::where('key','zgp_secret')->value('value') ?? '';

        $sign = "";
        if ($data['merchant_id'] != '') {
            $sign .= $data['merchant_id'];
        }
        if ($data['order_no'] != '') {
            $sign .= $data['order_no'];
        }
        if ($data['out_trade_no'] != '') {
            $sign .= $data['out_trade_no'];
        }
        if ($data['param'] != '') {
            $sign .= $data['param'];
        }
        if ($data['pay_time'] != '') {
            $sign .= $data['pay_time'];
        }
        if ($data['price'] != '') {
            $sign .= $data['price'];
        }
        $sign = md5($sign.$api_secret);

        if(strtoupper($sign) != $data['sign'])
        {
            echo "hash error";
        } else {
            DB::beginTransaction();
            try{
                // 请自行处理您的业务逻辑
                $item = Recharge::where('order_no',$data['order_no'])->where('state',1)->first();
                $item->state = 2;
                $item->save();
                $user = User::find($item->user_id);
                $user->balance += $data['amount'];
                $user->paysum += $data['amount'];
                $user->save();
                DB::commit();
                echo "success";
            }catch (\Exception $e) {
                DB::rollBack();
            }
        }
    }


    /**
     * 异步回调
     *
     * @param Request $request
     * @return void
     */
    public function fourwaynotify(Request $request)
    {
        if(strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') != 0){
            throw new Exception('Request method must be POST!');
        }
        //POST调用
        $data = $request->all();
        if(!is_array($data)){
            throw new Exception('Received content contained invalid JSON!');
        }
        $sign = "";
        if ($data['state'] != '') {
            $sign .= $data['state'];
        }

        if ($data['merchantNum'] != '') {
            $sign .= $data['merchantNum'];
        }
        if ($data['orderNo'] != '') {
            $sign .= $data['orderNo'];
        }
        if ($data['amount'] != '') {
            $sign .= $data['amount'];
        }

        $sign = md5($sign."e9afed057f49f46fc7518dd84135d73a");

        if($sign != $data['sign'])
        {
            echo "sign error";
        } else {
            DB::beginTransaction();
            try{
                $item = Recharge::where('order_no',$data['order_no'])->where('state',1)->first();
                $item->state = 2;
                $item->save();
                $user = User::find($item->user_id);
                $user->balance += $data['amount'];
                $user->paysum += $data['amount'];
                $user->save();
                DB::commit();
                echo "success";
            }catch (\Exception $e) {
                DB::rollBack();
            }
        }

    }


    public function zgpWithdrawCallback()
    {
        //POST调用
        $content = trim(file_get_contents("php://input"));
        //JSON转数据
        $data = json_decode($content, true);
        if(!is_array($data)){
            throw new \Exception('Received content contained invalid JSON!');
        }

        $api_secret = SystemConfig::where('key','zgp_secret')->value('value') ?? '';

        $sign = "";
        if ($data['apply_time'] != '') {
            $sign .= $data['apply_time'];
        }
        if ($data['is_auto'] != '') {
            $sign .= $data['is_auto'];
        }
        if ($data['merchant_id'] != '') {
            $sign .= $data['merchant_id'];
        }
        if ($data['notify_url'] != '') {
            $sign .= $data['notify_url'];
        }
        if ($data['order_no'] != '') {
            $sign .= $data['order_no'];
        }
        if ($data['out_trade_no'] != '') {
            $sign .= $data['out_trade_no'];
        }
        if ($data['total_fee'] != '') {
            $sign .= $data['total_fee'];
        }
        if ($data['user_name'] != '') {
            $sign .= $data['user_name'];
        }
        if ($data['user_wallet'] != '') {
            $sign .= $data['user_wallet'];
        }
        $sign = md5($sign.$api_secret);

        if(strtoupper($sign) != $data['Sign'])
        {
            echo "hash error";
        } else {
            // 请自行处理您的业务逻辑
            $item = Withdraw::where('order_no',$data['out_trade_no'])->first();
            $item->state = 2;
            $item->save();
            // 一定要返回success
            echo "success";
        }
    }

    public function bindCard()
    {
         $lang = $this->showlang;
        $balancelist = Usersmoney::getUserBalance(Auth::id());
        return view($this->path.'.member.bind_card',compact('balancelist','lang'));
    }

    public function bindZgpay()
    {
        return view($this->path.'.member.bind_zgpay');
    }

    public function bindCardDo(Request $request)
    {
        $data = $request->all();
        if($data['bank']=='USDT'){
            $count = UserCard::where('user_id', Auth::id())->where('bank','USDT')->count();
        } else {
            $count = UserCard::where('user_id', Auth::id())->where('bank','<>','USDT')->count();
        }

        $data['user_id'] = Auth::id();
        $res = UserCard::create($data);
        return $this->returnMsg($res ? 200 : 500);
    }

    /**
     * 修改银行卡信息
     */
    public function editCard($id,Request $request)
    {
        $lang = $this->showlang;
        if ($request->isMethod('post')) {
            $data = $request->all();
            $res = UserCard::where('id',$data['id'])->update($data);
            return $this->returnMsg($res ? 200 : 500);
        }
        return view($this->path.'.member.card_edit',compact('id','lang'));
    }

    /**
     * 获取银行卡信息
     */
    public function getCardData($id)
    {
        $item = UserCard::find($id);
        return $item;
    }

    public function delCard($id)
    {
        $res = UserCard::where('id',$id)->delete();
        return $this->returnMsg($res ? 200 : 500);
    }

    public function BalanceAll()
    {

        $user = Auth::user();
        $tg = new TgService;
        $result = $tg->allusersbalance($user->username);
        //dd($result);

        $Balance = $result['data']['userblance'];
        if($Balance){
            foreach ($Balance as $wo){
                Usersmoney::upinfo(Auth::id(),$wo['gamecode'],$wo['blance']);
            }
        }

        //print_r($Balance);
        return $this->returnMsg($Balance ? 200 : 500,$Balance,'');
        //return view($this->path.'.member.withdraw',compact('Balance'));
    }

    public function userAllBalance(){

        $user = Auth::user();
        $Balancelist = Usersmoney::getUserBalance(Auth::id());

        return $this->returnMsg($Balancelist ? 200 : 500,$Balancelist,"");
    }

    public function withdraw()
    {
        $lang = $this->showlang;
        $card_count = UserCard::where('user_id',Auth::id())->count();
        $cards = UserCard::where('user_id',Auth::id())->get();

        $list = Withdraw::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
        $bet_amount = User::vaildBetSum(Auth::id());
        foreach ($list as $k => $v) {
            $list[$k]['state'] = $this->state[$v->state];
        }

        $Balancelist = Usersmoney::getUserBalance(Auth::id());
        $cash_fee = SystemConfig::getValue('withdraw_cash_fee') ?? 0;
        $withdraw_fee_usdt_erc = SystemConfig::getValue('withdraw_fee_usdt_erc') ?? 0;
        $usdt_rate = SystemConfig::getValue('withdraw_usdt_rate') ?? 6.45;

        return view($this->path.'.member.withdraw',compact('card_count','cards','list','Balancelist','lang','bet_amount','cash_fee','usdt_rate','withdraw_fee_usdt_erc'));
    }

    public function withdrawApply(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $daily_withdraw_times = SystemConfig::getValue('daily_withdraw_times');
            $min_withdraw_money = SystemConfig::getValue('min_withdraw_money');
            $withdraw_fee = SystemConfig::getValue('withdraw_fee');
            $max_withdraw_money = SystemConfig::getValue('max_withdraw_money');
            $user = Auth::user();
            if (isset($daily_withdraw_times) && !empty($daily_withdraw_times)) {
                $count = Withdraw::whereDate('created_at',date('Y-m-d'))->count();
                if ($count >= $daily_withdraw_times) {
                    return $this->returnMsg(216);
                }
            }
            //时间限制
            $withdraw_begin_time = SystemConfig::getValue('withdraw_begin_time');
            $date = date('Y-m-d');
            if ($withdraw_begin_time) {
                $begin = $date.' '.$withdraw_begin_time;
                $begin_time = strtotime($begin);
                if (time() < $begin_time) return $this->returnMsg(218);
            }
            $withdraw_end_time = SystemConfig::getValue('withdraw_end_time');
            if ($withdraw_end_time) {
                $end = $date.' '.$withdraw_end_time;
                $end_time = strtotime($end);
                if (time() > $end_time) return $this->returnMsg(219);
            }

            $withdrawinfo = Withdraw::where('user_id',$user->id)->where('state',2)->orderBy("id","desc")->first();

            if($withdrawinfo){
                $recharge_amount = Recharge::where('user_id',$user->id)->where('state',2)->whereDate('created_at','>=',$withdrawinfo->created_at)->sum('amount');
                $bet_amount = GameRecord::where('user_id',$user->id)->whereDate('created_at','>=',$withdrawinfo->created_at)->sum('valid_amount');
            }else{
                $recharge_amount = Recharge::where('user_id',$user->id)->where('state',2)->where('state',2)->sum('amount');
                $bet_amount = GameRecord::where('user_id',$user->id)->sum('valid_amount');
            }

            if($recharge_amount > 0 && $bet_amount/$recharge_amount<$withdraw_fee){
                return $this->returnMsg(214,[],'打码量达没有达到充值的'.$withdraw_fee.'倍,无法正常提现');
            }
            if (isset($min_withdraw_money) && !empty($min_withdraw_money)) {
                if ($data['amount'] < $min_withdraw_money) {
                    return $this->returnMsg(214,[],'单次提款最低金额：'.$min_withdraw_money);
                }
            }
            if (isset($max_withdraw_money) && !empty($max_withdraw_money)) {
                if ($data['amount'] > $max_withdraw_money) {
                    return $this->returnMsg(215,[],'单次提款最高金额：'.$max_withdraw_money);
                }
            }

            if (!$data['password']){
                return $this->returnMsg(520,[],'请输入取款密码');
            }else{
                if(empty($user->paypwd)){
                    return $this->returnMsg(520,[],'请先设置取款密码');
                }else{
                    if (!Hash::check($data['password'],$user->paypwd))  return $this->returnMsg(520,[],'取款密码错误');
                }

            }
            

            if ($data['amount'] > $user->balance) return $this->returnMsg(208);
            //提现
            $card = UserCard::find($data['bank']);
            $order_no = time().rand(1000,9999);
            if ($card['bank'] == 'ZGPay') {
                $merchant_id = SystemConfig::where('key','merchant_id')->value('value') ?? '';
                $api_secret = SystemConfig::where('key','zgp_secret')->value('value') ?? '';
                $zgpay = new Zgpay($merchant_id,$api_secret);
                $res = $zgpay->withdraw($order_no,$data['amount'],$card['bank_owner'],$card['bank_no']);
                $res = json_decode($res,true);
                if ($res['code'] != 200) return $this->returnMsg(500);
            }
            $user->balance -= $data['amount'];
            $user->save();
            $usdt_rate = SystemConfig::getValue('withdraw_usdt_rate');
            if (!isset($data['type'])) $data['type'] = $card['bank'] == 'USDT' ? 2 : 1;
            if ($card['bank_address'] == 'TRC20') {
                $cash_fee = SystemConfig::getValue('withdraw_cash_fee') ?? 0;
                $real_money = sprintf('%.2f',$data['amount'] / $usdt_rate);
                $real_money -= $cash_fee;
            } elseif ($card['bank_address'] == 'ERC20') {
                $cash_fee = SystemConfig::getValue('withdraw_fee_usdt_erc') ?? 0;
                $real_money = sprintf('%.2f',$data['amount'] / $usdt_rate);
                $real_money -= $cash_fee;
            } else {
                $cash_fee = 0;
                $real_money = $data['amount'];
            }
            // 插入提现记录
            $item = [
                'order_no' => $order_no,
                'card_id' => $data['bank'],
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'cash_fee' => $cash_fee,
                'real_money' => $real_money,
                'type' => $data['type'],
                'usdt_rate' => ($data['type'] == 1) ? 0 : $usdt_rate
            ];
            $res = Withdraw::create($item);
            return $this->returnMsg($res ? 200 : 500);
        }
        $withdrawinfo = Withdraw::where('user_id',Auth::id())->where('state',2)->orderBy("id","desc")->first();


        if($withdrawinfo){
            $betamount = GameRecord::where('user_id',Auth::id())->whereDate('created_at','>=',$withdrawinfo->created_at)->sum('valid_amount');
        }else{
            $betamount = GameRecord::where('user_id',Auth::id())->sum('valid_amount');
        }
        $lang = $this->showlang;
        return view($this->path.'.member.withdraw_apply',compact('betamount','lang'));
    }

    public function getAllUserCard()
    {
        $list['bank'] = UserCard::where('user_id',Auth::id())->where("bank","<>","USDT")->get();
        $list['zgpay'] = UserCard::where('user_id',Auth::id())->where("bank","=","USDT")->first();
        $list['usdt'] = UserCard::where('user_id',Auth::id())->where("bank","=","USDT")->get();
        return $this->returnMsg(200,$list);
    }

    public function transAll()
    {
        $tg = new TgService;
        $user = Auth::user();

        $result = $tg->recoverallbalance($user->username);
        \Illuminate\Support\Facades\Log::info("前端一键回收结果".$user->username);

        \Illuminate\Support\Facades\Log::info($result);        
        //$result = json_decode($result,true);
        $blance = 0;
        if($result['code']==0){
/*            foreach ($result['data']['userblance'] as $val){
                if($val['success']=="ok" && $val['blance']>0){
                     $user->AllAccounttranso($val['gamecode'], $val['blance']);
                     //Usersmoney::kouinfo($this->id, $plat_name, $money);
                      Usersmoney::setmoneyinit($user->id, $val['gamecode']);
                     $blance +=$val['blance']; 
                }elseif($val['success']=="ok" && $val['blance']==0){
                      Usersmoney::setmoneyinit($user->id, $val['gamecode']);
                }
            }*/
            $blance = round($result['data']['userblance'],2);            
            if($blance>0){
             return $this->returnMsg(200,'','共回收金额：'.$blance);    
            }else{
             return $this->returnMsg(200,'','没有可回收的金额'.$blance);    
            }
             
        }else{
             return $this->returnMsg(500,[],$result['msg']);
        }
        //$result = $tg->transAll($user->username);
/*        $blan = $result['data'];
        if ($blan) {
            foreach ($blan as $key=>$val){
                $user->Accounttranso($key, $val);
            }
            return $this->returnMsg(200,$user->balance);
        }else{
            return $this->returnMsg(500,[],'没有可回收的金额');
        }*/
    }

    /**
     * 钱包
     *
     * @return void
     */
    public function wallet()
    {
        $card_count = UserCard::where('user_id',Auth::id())->count();
        $cards = UserCard::where('user_id',Auth::id())->get();
        $Balancelist = Usersmoney::getUserBalance(Auth::id());
         $lang = $this->showlang;
        return view($this->path.'.member.wallet',compact('card_count','cards','Balancelist','lang'));
    }

    public function getUserBalance(Request $request)
    {
        $type = $request->input('type') ?? '';
        $user = Auth::user();
        if ($type == 'system') {
            return $this->returnMsg(200,$user->balance);
        }
        $tg = new TgService;
        $result = $tg->allusersbalance($user->username);
        if ($result['code'] != 200) {
            return $this->returnMsg(500,[],$result['message']);
        }

    }
    public function getUserPlBalance(Request $request)
    {
        $type = $request->input('type') ?? '';
        $user = Auth::user();
        $tg = new TgService;
        $result = $tg->userBalance($user->username,$type);
        if ($result['code'] != 200) {
            return $this->returnMsg(500,[],$result['message']);
        }
        Usersmoney::upinfo($user->id,$type,$result['data']);
        return $this->returnMsg(200,$result['data']);
    }
    /**
     * 额度转换
     *
     * @param Request $request
     * @return void
     */
    public function transfer(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $tg = new TgService;
        $order_no = time().rand(1000,9999);
        if ($data['type'] == 'in') { //转入系统
            $amount = -1 * abs($data['amount']);
            $res = $tg->trans($user->username,$amount,$order_no);
            if ($res['code'] == 200) {
                $user->balance += abs($data['amount']);
                $user->save();
                $arr = [
                    'order_no' => $order_no,
                    'api_type' => 'ag',
                    'user_id' => $user->id,
                    'transfer_type' => 1,
                    'money' => $amount,
                    'cash_fee' => 0,
                    'real_money' => $amount,
                    'before_money' => $user->balance - $amount,
                    'after_money' => $user->balance,
                    'state' => 1
                ];
                TransferLog::create($arr);
                return $this->returnMsg(200,$user->balance);
            } else {
                return $this->returnMsg(209);
            }
        } else {
            $amount = abs($data['amount']);
            if ($amount > $user->balance) return $this->returnMsg(210);

            $res = $tg->trans($user->username,$amount,$order_no);
            if ($res['code'] == 200) {
                $user->balance -= $data['amount'];
                $user->save();
                $arr = [
                    'order_no' => $order_no,
                    'api_type' => 'ag',
                    'user_id' => $user->id,
                    'transfer_type' => 0,
                    'money' => $amount,
                    'cash_fee' => 0,
                    'real_money' => $amount,
                    'before_money' => $user->balance + $amount,
                    'after_money' => $user->balance,
                    'state' => 1
                ];
                TransferLog::create($arr);
                return $this->returnMsg(200,$user->balance);
            } else {
                return $this->returnMsg(209);
            }
        }
    }

}