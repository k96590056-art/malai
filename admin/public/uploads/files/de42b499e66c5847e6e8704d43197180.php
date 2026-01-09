<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\Auth;
use App\Models\Recharge;
use App\Models\CodePay;
use App\Models\GameRecord;
use App\Models\PaySetting;
use App\Models\TransferLog;
use App\Models\UserCard;
use App\Models\Usersmoney;
use App\Models\Withdraw;
use App\Models\User;
use App\Models\UserVip;
use App\Services\TgService;
use Illuminate\Support\Facades\Hash;
use App\Models\Userredpacket;
use App\Models\RedEnvelopes;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    protected $messages = [];
    protected $banklist;

    public function __construct()
    {

        $this->banklist = ['中国工商银行'=>'Icbc','工商银行'=>'Icbc','中国农业银行'=>'Abc','招商银行'=>'Cmb','中国建设银行'=>'Ccb','中信银行'=>'Cibk','中国银行'=>'Boc','交通银行'=>'Bocom','华夏银行'=>'Hxbc','民生银行'=>'Cmbc','光大银行'=>'Cebc','建设银行'=>'Ccb'];

    }
    /**
     * 系统银行卡信息
     */
    public function systemBankCardInfo()
    {
        $card = PaySetting::where('state', 1)->first();
        return $this->returnMsg(200, $card);
    }
    /**
     * 系统银行卡信息
     */
    public function refreshusermoney(Request $request)
    {
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
     $user = User::where('api_token',$token)->first(); 
        $tg = new TgService;
        $result = $tg->allusersbalance($user->username);        
        $Balance = $result['data']['userblance'];
        $str = "";
        $gameblance = 0;
        if($Balance){
            foreach ($Balance as $wo){
                Usersmoney::upinfo($user->id,$wo['gamecode'],$wo['blance']);
                $gameblance +=$wo['blance'];
            }
        }
        $info = SystemConfig::where('key','usdt_rate')->first();
        $money = TransferLog::where('user_id',$user->id)
            ->where('state', 0)->sum('money');
        $info_withdraw = SystemConfig::where('key','withdraw_usdt_rate')->first();
        $user->fanshui = $money;
        $data['api_token'] = $user->api_token;
        $data['balance'] = $user->balance;
        $data['birthday'] = $user->birthday;
        $data['fanshui'] = $user->fanshui;
        $data['realname'] = $user->realname;
        $data['transferstatus'] = $user->transferstatus;
        $data['username'] = $user->username;
        $data['vip'] = $user->vip;
        $data['level'] = $user->level;
        $data['joinday'] = intval((time()-strtotime($user->created_at))/60/60/24);
        $data['gameblance'] =Usersmoney::getTotalAppUserBalance($user->id);
        $data['avatar'] = ($user->avatar) ? env('APP_URL').$user->avatar : '';
        $data['mobile'] = $user->phone;
        $data['email'] = $user->mail;
        $data['birthday'] = $user->birthday;
        $data['usdtrate'] = $info->value;
        $data['withdrawusdtrate'] =$info_withdraw->value;   
        $info_withdrawcashfee = SystemConfig::where('key','withdraw_fee_usdt_erc')->first();
        $data['withdrawcashfee'] =$info_withdrawcashfee->value;  
        $info_withdrawfeeusdttrc = SystemConfig::where('key','withdraw_cash_fee')->first();
        $data['withdrawfeeusdttrc'] =$info_withdrawfeeusdttrc->value; 
        $uservip = UserVip::where('id',$user->vip)->first();
        if($uservip){
                $data['vipname'] =  '/static/style/'.strtolower($uservip->vipname).'.png';
            }else{
                $data['vipname'] =  '/static/style/'.strtolower('VIP0').'.png';
         }

         
        return $this->returnMsg(200, $data,'刷新成功');
    }
    /**
     * 系统银行卡信息
     */
    public function getpayinfo(Request $request)
    {
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
     $user = User::where('api_token',$token)->first(); 
     
        $data = $request->all();
        $info = Recharge::where('user_id',$user->id)->where('out_trade_no',$data['deposit_no'])->first();
        switch ($info['pay_way']) {
            case 1: //提交后台审核
                $cardlist = PaySetting::where('state',1)->get();
                foreach ($cardlist as &$val){
                    if($val->bank_data->bank_name!='USDT' || $val->bank_data->bank_name!='银行类型后台添加'){
                        $val->ico= 'http://tggood.com/bank/'. $this->banklist[$val->bank_data->bank_name].'.png';    
                    }else{
                        $val->ico='';
                    }
                }
                $info->paytype='银行转账支付';
                $data['info'] = $info;
                $data['cardlist'] = $cardlist;
                return $this->returnMsg($data ? 200 : 500,$data,'bankpay');
                break;
            case 3: //提交后台审核  alipay
                $alipayinfo = CodePay::where('status',1)->where('id',4)->first();
                $alipayinfo->payimg = env('APP_URL').'/uploads/'.$alipayinfo->payimg;
                $info->paytype='支付宝扫码支付';
                $data['info'] = $info;
                $data['cardlist'] = $alipayinfo;                
                return $this->returnMsg($data ? 200 : 500,$data,'alipay');
                break;
            case 4: //提交后台审核  wxpay
                $wxinfo = CodePay::where('status',1)->where('id',3)->first();
                $wxinfo->payimg = env('APP_URL').'/uploads/'.$wxinfo->payimg;
                $info->paytype='微信扫码支付';
                $data['info'] = $info;
                $data['cardlist'] = $wxinfo;                    
                return $this->returnMsg($data ? 200 : 500,$data,'wxpay');
                break;
           case 5: //提交后台审核  USDT
                $infousd = SystemConfig::where('key','usdt_rate')->first();
                $usdtinfo = CodePay::where('status',1)->where('id',5)->first();    
                $usdtinfo->payimg = env('APP_URL').'/uploads/'.$usdtinfo->payimg;
                $info->paytype='USDT扫码支付';
                $info->usdtrate = $infousd->value;
                $info->real_money = round($info->real_money / $infousd->value,2);
                $data['info'] = $info;
                $data['cardlist'] = $usdtinfo;                   
                return $this->returnMsg($data ? 200 : 500,$data,'usdtpay');
                break;
           case 6: //提交后台审核  USDT
                $infousd = SystemConfig::where('key','usdt_rate')->first();
                
                $usdtinfo = CodePay::where('status',1)->where('id',7)->first();        
               
                $usdtinfo->payimg = env('APP_URL').'/uploads/'.$usdtinfo->payimg;
                $info->paytype='USDT扫码支付';
                $info->usdtrate = $infousd->value;
                $info->real_money = round($info->real_money / $infousd->value,2);
                $data['info'] = $info;
                $data['cardlist'] = $usdtinfo;                   
                return $this->returnMsg($data ? 200 : 500,$data,'usdtpay');
                break;     
            case 7:
                $ebpay = CodePay::where('status',1)->where('id',8)->first();
                $ebpay->payimg = env('APP_URL').'/uploads/'.$ebpay->payimg;
                $info->paytype='EBpay';
                $data['info'] = $info;
                $data['cardlist'] = $ebpay;                    
                return $this->returnMsg($data ? 200 : 500,$data,'ebpay');
                break;
            default:
                $cardlist = PaySetting::where('state',1)->get();
                foreach ($cardlist as &$val){
                    if($val->bank_data->bank_name!='USDT' || $val->bank_data->bank_name!='银行类型后台添加'){
                        $val->ico= 'http://tggood.com/bank/'. $this->banklist[$val->bank_data->bank_name].'.png';    
                    }else{
                        $val->ico='';
                    }
                }
                $data['info'] = $info;
                $data['cardlist'] = $cardlist;
                return $this->returnMsg($data ? 200 : 500,$data,'bankpay');
                break;
        }       
       
    }



        
    /**
     * 充值
     *
     * @param Request $request
     * @return void
     */
    public function recharge(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'paytype' => 'required',
        ];
        $this->validate($request, $rules, $this->messages);
      
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 

        $data = $request->all();
        $min_recharge_money = SystemConfig::getValue('min_recharge_money');
        $max_recharge_money = SystemConfig::getValue('max_recharge_money');
        if (isset($min_recharge_money) && !empty($min_recharge_money)) {
            if ($data['amount'] < $min_recharge_money) {
                return $this->returnMsg(500,[],'单次充值最低金额：'.$min_recharge_money);
            }
        }
        if (isset($max_recharge_money) && !empty($max_recharge_money)) {
            if ($data['amount'] > $max_recharge_money) {
                return $this->returnMsg(500,[],'单次充值最高金额：'.$max_recharge_money);
            }
        }
        $out_trade_no = time().$user->id.rand(1000,9999);
        $data['out_trade_no'] = $out_trade_no;
        $data['user_id'] = $user->id;
        $data['pay_way'] = $data['paytype'];
        $catepay = $data['catepay'] ?? '';
        unset($data['catepay']);
        unset($data['paytype']);
        
        switch ($data['pay_way']) {
            case "bank": //提交后台审核
                $data['pay_way'] =1;
                $data['cash_fee'] = 0;
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $min_price = SystemConfig::getValue('min_price');
                $max_price = SystemConfig::getValue('max_price');
                if ($data['amount'] > $max_price || $data['amount'] < $min_price) return $this->returnMsg(500,[],'充值金额不在该通道范围中');
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500,[],$data['out_trade_no']);
                break;
            case "alipay": //提交后台审核  alipay
                $data['cash_fee'] = 0;
                $data['pay_way'] =3;
                $usdtinfo = CodePay::where('status',1)->where('id',4)->first();
                if(!$usdtinfo){
                     return $this->returnMsg(500,[],'系统维护中...');
                }
                if ($data['amount'] > $usdtinfo['max_price'] || $data['amount'] < $usdtinfo['min_price']) return $this->returnMsg(500,[],'充值金额不在该通道范围中');
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $res = Recharge::create($data);
                 return $this->returnMsg($res ? 200 : 500,[],$data['out_trade_no']);
                break;
            case "wxpay": //提交后台审核  wxpay
                $data['cash_fee'] = 0;
                $data['pay_way'] =4;
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $usdtinfo = CodePay::where('status',1)->where('id',3)->first();
                if(!$usdtinfo){
                     return $this->returnMsg(500,[],'系统维护中...');
                }           
                if ($data['amount'] > $usdtinfo['max_price'] || $data['amount'] < $usdtinfo['min_price']) return $this->returnMsg(500,[],'充值金额不在该通道范围中');
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500,[],$data['out_trade_no']);
                break;
           case "usdt": //提交后台审核  USDT
                $data['cash_fee'] = 0;
                $data['bank'] = $catepay;
                $data['pay_way'] = ($catepay=='TRC20') ? 5 : 6;
                $data['usdt_rate'] = SystemConfig::getValue('usdt_rate');
                
                $pay_way = ($catepay=='TRC20') ? 5 : 7;
                
                $usdtinfo = CodePay::where('status',1)->where('id',$pay_way)->first();
                if(!$usdtinfo){
                     return $this->returnMsg(500,[],'系统维护中...');
                }   
                if ($data['amount'] > $usdtinfo['max_price'] || $data['amount'] < $usdtinfo['min_price']) return $this->returnMsg(500,[],'充值金额不在该通道范围中');
                $data['real_money'] = sprintf('%.2f',$data['amount'] / $data['usdt_rate']);
                // $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500,[],$data['out_trade_no']);
                break;
            case 'ebpay':
                $data['bank'] = 'ebpay';
        	    $data['pay_way'] = 7;
                $data['cash_fee'] = 0;
                $data['real_money'] = $data['amount'] - $data['cash_fee'];
                $info = CodePay::where('status',1)->where('id',8)->first();
                if(!$info){
                    return $this->returnMsg(500,[],'系统维护中...');
                } 
                if ($data['amount'] > $info['max_price'] || $data['amount'] < $info['min_price']) return $this->returnMsg(500,[],'充值金额不在该通道范围中');
                $res = Recharge::create($data);
                return $this->returnMsg($res ? 200 : 500,[],$data['out_trade_no']);
            	

            default:
                # code...
                break;
        }
    }
    
    public function getPayRange(Request $request)
    {
        $type = $request->input('type');
        switch ($type) {
            case 'bank':
                $pay_way = 0;
                break;
            case 'alipay':
                $pay_way = 4;
                break;
            case 'wechat':
                $pay_way = 3;
                break;
            case 'usdt-erc20':
                $pay_way = 7;
                break;
            case 'usdt-trc20':
                $pay_way = 5;
                break;
            case 'ebpay':
                $pay_way = 8;
                break;
            default:
                $pay_way = 0;
                break;
        }
        $data = ['min_price' => 0,'max_price' => 0];
        if ($pay_way > 0) {
            $range = CodePay::where('id',$pay_way)->select('min_price','max_price')->first();
            if ($range) $data = ['min_price' => $range->min_price,'max_price' => $range->max_price];
        } else {
            $min_price = SystemConfig::getValue('min_price') ?? 0;
            $max_price = SystemConfig::getValue('max_price') ?? 0;
            $data = ['min_price' => $min_price,'max_price' => $max_price];
        }
        return $this->returnMsg(200,$data);
    }
    
    /**
     * 绑定银行卡
     *
     * @param Request $request
     * @return void
     */
    public function bindCard(Request $request)
    {
        $rules = [
            'bank' => 'required',
            'bank_no' => 'required',
            'bank_owner' => 'nullable',
            'pay_pass' => 'required',
            'bank_address' => 'nullable',
        ];
         $this->validate($request, $rules, $this->messages);
         $data = $request->all();
         $token = $request->header('authorization');
         $token = str_replace('Bearer ','',$token) ;
         $user = User::where('api_token',$token)->first(); 
        if(!$user->paypwd){
            return $this->returnMsg(251,[],'请先设置支付密码');
        }
        //if (!Hash::check($data['pay_pass'], $user->paypwd)) return $this->returnMsg(205,[],'支付密码错误');
        
        
        if($data['bank']=='USDT'){
            $count = UserCard::where('user_id', $user->id)->where('bank','USDT')->count();
            $data['bank_address'] = $data['bank_owner'];
          // $usdtinfo = UserCard::where('user_id', $user->id)->where('bank', 'USDT')->first();
           //if($usdtinfo){
             //  $usdtinfo->bank_owner = $data['bank_owner'];
             //  $usdtinfo->bank_no = $data['bank_no'];
             //  $usdtinfo->bank_address = $data['bank_address'];
             //  $usdtinfo->save();
           //}
        } else {
            $count = UserCard::where('user_id', $user->id)->where('bank','<>','USDT')->count();
        }

        unset($data['pay_pass']);
        if ($count > 5) return $this->returnMsg(207,[],'最多只能绑定5张银行卡');
        $data['user_id'] = $user->id;
        $res = UserCard::create($data);
        return $this->returnMsg($res ? 200 : 500);
    }
    /**
     * 绑定银行卡
     *
     * @param Request $request
     * @return void
     */
    public function DelbindCard(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];
        $this->validate($request, $rules, $this->messages);
        $data = $request->all();
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $count = UserCard::where('user_id', $user->id)->where('id', $data['id'])->delete();
        return $this->returnMsg($count ? 200 : 500);
    }
    
    public function getBetAmount(Request $request)
    {
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $withdrawinfo = Withdraw::where('user_id',$user->id)->where('state',2)->orderBy("id","desc")->first();

        if($withdrawinfo){
            $recharge_amount = Recharge::where('user_id',$user->id)->where('state',2)->whereDate('created_at','>=',$withdrawinfo->created_at)->sum('amount');
            $bet_amount = GameRecord::where('user_id',$user->id)->whereDate('created_at','>=',$withdrawinfo->created_at)->sum('valid_amount');
        }else{
            $recharge_amount = Recharge::where('user_id',$user->id)->where('state',2)->where('state',2)->sum('amount');
            $bet_amount = GameRecord::where('user_id',$user->id)->sum('valid_amount');
        }
        return $this->returnMsg(200,compact('bet_amount'));
    }
    /**
     * 提现
     *
     * @param Request $request
     * @return void
     */
    public function withdraw(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'bank' => 'required',
            'password' => 'required',
        ];
        $this->validate($request,$rules,$this->messages);

        $data = $request->all();
        $daily_withdraw_times = SystemConfig::getValue('daily_withdraw_times');
        $min_withdraw_money = SystemConfig::getValue('min_withdraw_money');
        $withdraw_fee = SystemConfig::getValue('withdraw_fee');
        $max_withdraw_money = SystemConfig::getValue('max_withdraw_money');
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 
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
        // return $this->returnMsg(200,$card);
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
         $type = 1;
         $cash_fee = 0;
        // 插入提现记录
        $usdt_rate = SystemConfig::getValue('withdraw_usdt_rate');
        if($card['bank']=='USDT' && ($card['bank_address']=='TRC20' || $card['bank_owner']=='TRC20')){
            $type = 2;
            $cash_fee = SystemConfig::getValue('withdraw_cash_fee') ?? 0;
            $real_money = sprintf('%.2f',$data['amount'] / $usdt_rate);
            $real_money -= $cash_fee;
        }elseif($card['bank']=='USDT' && ($card['bank_address']=='ERC20' || $card['bank_owner']=='ERC20')){
             $type = 3;
             $cash_fee = SystemConfig::getValue('withdraw_fee_usdt_erc') ?? 0;
            $real_money = sprintf('%.2f',$data['amount'] / $usdt_rate);
            $real_money -= $cash_fee;
        } elseif ($card['bank'] == 'ebpay') {
            $type = 4;
            $real_money = $data['amount'];
        }else {
            $real_money = $data['amount'];
        }
        
        $item = [
            'order_no' => $order_no,
            'type' => $type,
            'card_id' => $data['bank'],
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'cash_fee' => $cash_fee,
            'real_money' => $real_money,
            'usdt_rate' => ($type == 1) ? 0 : $usdt_rate,
            'bet_amount' => $bet_amount
        ];
        $res = Withdraw::create($item);
        return $this->returnMsg($res ? 200 : 500);
    }

    /**
     * 用户所有银行卡
     */
    public function getAllUserCard(Request $request)
    {
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
     $user = User::where('api_token',$token)->first();
     $data = $request->all();
     if($data['type']==1){
        $list = UserCard::where('user_id', $user->id)->whereNotIn('bank',['USDT','ebpay'])->get();       
     }elseif ($data['type'] == 2){
         $list = UserCard::where('user_id', $user->id)->where('bank','USDT')->get();        
     } elseif ($data['type'] == 3) {
         $list = UserCard::where('user_id', $user->id)->where('bank','ebpay')->get();
     }
  
  
     $info = SystemConfig::where('key','usdt_rate')->first();
     $info_withdraw = SystemConfig::where('key','withdraw_usdt_rate')->first();
     foreach ($list as &$val){
        if($val->bank!='USDT' && $val->bank != 'ebpay'){
            $icon = $this->banklist[$val->bank] ?? '';
            $val->ico= $icon ? 'http://tggood.com/bank/'. $icon.'.png' : '';    
        }else{
                $val->ico='';
        }
            $val->bank_not=substr($val->bank_no,-4);
            $val->usdtrate=$info->value;
            $val->withdrawusdtrate=$info_withdraw->value;
            
        }        
        return $this->returnMsg(200, $list);
    }

    /**
     * 额度转换
     *
     * @param Request $request
     * @return void
     */
    public function transfer(Request $request)
    {
        $rules = [
            'sourcetype' => 'required',
            'targettype'=>'required',
            'amount' => 'required'
        ];
        $this->validate($request,$rules,$this->messages);
        $data = $request->all();
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 
        $tg = new TgService;
        $order_no = time().rand(1000,9999);
        if($data['sourcetype']==$data['targettype']){
             return $this->returnMsg(209,[],'来源和目标是一致，没有必要转了');
        }elseif($data['sourcetype']=="userbalance"){
            $data['type'] = "togame";
            $data['pay_way'] =$data['targettype'];
        }elseif($data['targettype']=="userbalance"){
            $data['type'] = "toaccount";
            $data['pay_way'] =$data['sourcetype'];
        }
        if ($data['type'] == "togame") { //转入游戏
            $amount = $data['amount'];
            if ($amount > $user->balance) return $this->returnMsg(210,[],'操作金额高于账户余额');
                $arr = [
                    'order_no' => $order_no,
                    'api_type' => $data['pay_way'],
                    'user_id' => $user->id,
                    'transfer_type' => 0,
                    'money' => $amount,
                    'cash_fee' => 0,
                    'real_money' => $amount,
                    'before_money' => $user->balance ,
                    'after_money' => $user->balance,
                    'state' => 0
                ];
                TransferLog::create($arr);               
            
            $res = $tg->trans($user->username,$amount,$order_no,$data['pay_way']);

            if ($res['code'] == 200) {
                $user->balance -= abs($data['amount']);
                $user->save();
                $transferlog = TransferLog::where('order_no', $order_no)->first();
                $transferlog->after_money = $user->balance-$amount;
                $transferlog->state = 1;
                $transferlog->save();
                Usersmoney::addinfo($user->id, $data['pay_way'], $amount);
                return $this->returnMsg(200,['balance' => $user->balance]);
            } else {
                return $this->returnMsg(209,$res,$res['message']);
            }
        } else {  //回收
            $amount = abs($data['amount']);
          
                $arr = [
                    'order_no' => $order_no,
                    'api_type' => $data['pay_way'],
                    'user_id' => $user->id,
                    'transfer_type' => 1,
                    'money' => $amount,
                    'cash_fee' => 0,
                    'real_money' => $amount,
                    'before_money' => $user->balance,
                    'after_money' => $user->balance,
                    'state' => 0
                ];
                TransferLog::create($arr);   
            $res = $tg->trans($user->username,-$amount,$order_no,$data['pay_way']);
            if ($res['code'] == 200) {
                $user->balance += $data['amount'];
                $user->save();
                $transferlog = TransferLog::where('order_no', $order_no)->first();
                $transferlog->after_money = $user->balance+$amount;
                $transferlog->state = 1;
                Usersmoney::kouinfo($user->id, $data['pay_way'], $amount);
                return $this->returnMsg(200,['balance' => $user->balance]);
            } else {
                return $this->returnMsg(209,$res,$res['message']);
            }
        }
    }
    

    public function transAll(Request $request)
    {
        $tg = new TgService;
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $result = $tg->recoverallbalance($user->username);
        $blance = 0;
        
        \Illuminate\Support\Facades\Log::info("手机版一键回收结果".$user->username);

        \Illuminate\Support\Facades\Log::info($result);  
        
        if($result['code']==0){
            /*foreach ($result['data']['userblance'] as $val){
                if($val['success']=="ok" && $val['blance']>0){
                     $this->AllAccounttranso($user,$val['gamecode'], $val['blance']);
                     //Usersmoney::kouinfo($this->id, $plat_name, $money);
                     $blance +=$val['blance'];
                      Usersmoney::setmoneyinit($user->id, $val['gamecode']);
                }elseif($val['success']=="ok" && $val['blance']==0){
                      Usersmoney::setmoneyinit($user->id, $val['gamecode']);
                }
            }*/
            $blance = round($result['data']['userblance'],2);
            $totaluserBlance = round($result['data']['totaluserBlance'],2);
            if($blance>0 && $totaluserBlance==$blance){
             return $this->returnMsg(200,'',"回收成功".$blance);    
            }elseif($totaluserBlance==0){
             return $this->returnMsg(200,'',"没有可回收的金额");    
            }{
             return $this->returnMsg(200,'',"成功回收".$blance.",部分金额因网络问无法正常回收，请稍后在试");    
            }
             
        }else{
             return $this->returnMsg(500,[],$result['msg']);
        }
    }
    

    /**
     * 一键回收
     * @return void
     */
    public function AllAccounttranso($user,$plat_name, $money)   // 游戏转账到余额
    {
        $client_transfer_id = time() . $user->id . rand(1000, 9999);
        $amount = abs($money);
        $arr = [
            'order_no' => $client_transfer_id,
            'api_type' => $plat_name,
            'user_id' => $user->id,
            'transfer_type' => 1,
            'money' => $money,
            'cash_fee' => 0,
            'real_money' => $amount,
            'before_money' => $user->balance ,
            'after_money' => $user->balance + $amount,
            'state' => 1
        ];
        TransferLog::create($arr);
        $user->balance = $user->balance + $money;
        $user->save();
        
        return array('code' => 200);
       
    }
    
    public function getPayWay()
    {
        $wxinfo = CodePay::where('status',1)->where('id',3)->count();
        $usdtinfo = CodePay::where('status',1)->where('id',5)->count();
        $usdtinfo_erc = CodePay::where('status',1)->where('id',7)->count();
        $alipayinfo = CodePay::where('status',1)->where('id',4)->count();
        $cardlist = PaySetting::where('state',1)->get();
        $wechat = $wxinfo ? 1 : 0;
        $usdt = ($usdtinfo || $usdtinfo_erc) ? 1 : 0;
        $alipay = $alipayinfo ? 1 : 0;
        $card = count($cardlist) > 0 ? 1 : 0;
        return $this->returnMsg(200,compact('wechat','usdt','alipay','card'),'success');
    }
    
    public function userRedPacket(Request $request)
    {
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        list($start, $end) = [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'];

        $acquirednum = Userredpacket::where('uid', $user->id)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->count();

        $totalRecharge = Recharge::where('user_id', $user->id)->where('state',2)->where('pay_way','<>',10)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->sum('amount');
            // var_dump($totalRecharge);exit;
  
        $rule = RedEnvelopes::where('flow_money','>=',$totalRecharge)->where('day_flow','<=',$totalRecharge)
            ->where('start_time','<',date('Y-m-d H:i:s'))->where('end_time','>',date('Y-m-d H:i:s'))->where('status',1)->orderBy('recharge','desc')->first();
        if (!$rule) {
            $sendnums = 0;
        } else {
            $sendnums = $rule->recharge;
        }
        $sendnums = $sendnums - $acquirednum;

        $rules = RedEnvelopes::where('status',1)->get();
        $data = date('Y-m-d');
        $datatime = date('Y-m-d H:i:s');
        $redPacketStatus = "READY";
        
        $max_times = RedEnvelopes::where('status',1)->orderBy('recharge','desc')->value('recharge');
        $max_times = intval($max_times);
        $max_end_time = RedEnvelopes::where('status',1)->orderBy('end_time','desc')->value('end_time');
        $min_start_time = RedEnvelopes::where('status',1)->orderBy('start_time')->value('start_time');
        if (!$rule) {
            $redPacketStatus = "END";
        } else {
            if (time() < strtotime($max_end_time) && time() > strtotime($min_start_time)) {
                $redPacketStatus = "STARTING";
            } elseif (time() > strtotime($max_end_time)) {
                $redPacketStatus = "END";
            } elseif (time() < strtotime($min_start_time)) {
                $redPacketStatus = "READY";
            }
        }
        return $this->returnMsg(200,compact('sendnums','acquirednum','date','datetime','redPacketStatus','rules','max_times'));
    }
    
    public function doUserRedPacket(Request $request)
    {
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $data = $request->all();
        list($start, $end) = [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'];

        $time = date('Y-m-d H:i:s');

        if($time<$start || $time>$end){
            return $this->returnMsg(202, '','时间未到或者已过，无法领取');
        }
        // if(time()-($data['time']/1000)>3){
        //     return $this->returnMsg(203, '','非法操作');
        // }
        $acquirednum = Userredpacket::where('uid', $user->id)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->count();

        $totalRecharge = Recharge::where('user_id', $user->id)->where('state',2)
            ->where('pay_way','<>',10)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->sum('amount');
        // $condition  = array(['amount'=>100000,'nums'=>10],['amount'=>50000,'nums'=>8],['amount'=>10000,'nums'=>5],['amount'=>5000,'nums'=>3],['amount'=>1000,'nums'=>1]);
        $rule = RedEnvelopes::where('flow_money','>=',$totalRecharge)->where('day_flow','<=',$totalRecharge)
            ->where('start_time','<',date('Y-m-d H:i:s'))->where('end_time','>',date('Y-m-d H:i:s'))->orderBy('recharge','desc')->first();
        if (!$rule) return $this->returnMsg(500,[],'暂无红包可抢');
        $sendnums = (int)$rule->recharge;
 
        if($sendnums<=0){
            return $this->returnMsg(203, '','累计充值不满足活动条件，无法领取');
        }
        if($acquirednum>=$sendnums){
            return $this->returnMsg(203, '','领取次数已超过奖励次数，无法领取');
        }
        //$redpacketmoney = $totalRecharge*0.02;

        $redpacketmoney = $this->randFloat(1,bcdiv($totalRecharge*$rule->money,100,2));

        $userinfo = User::where('id', $user->id)->lockForUpdate()->first();
        $userinfo->balance = $userinfo->balance + $redpacketmoney;
        $userinfo->save();
/*        $userredpacket->status = 1;
        $userredpacket->usetime = date('Y-m-d H:i:s');
        $userredpacket->save();*/
        $arrs = [
            'redpacketid' => $rule->id,
            'redpacketfee' => $rule->money,
            'uid' => $userinfo->id,
            'money' => $totalRecharge,
            'status' => 1,
            'redpacketmoney' =>$redpacketmoney,
            'usetime' => date('Y-m-d H:i:s'),
            'isuse' => 1
        ];
        Userredpacket::create($arrs);
        $arr = [
            'order_no' => date('Ymd') . '_' . $userinfo->id . '_' . time(),
            'api_type' => 'web',
            'user_id' => $userinfo->id,
            'transfer_type' => 5,
            'money' => $redpacketmoney,
            'cash_fee' => 0,
            'real_money' => $redpacketmoney,
            'before_money' => $userinfo->balance - $redpacketmoney,
            'after_money' => $userinfo->balance,
            'state' => 1
        ];
        TransferLog::create($arr);
        return $this->returnMsg(200, array('redpacketmoney'=>$redpacketmoney,'sendnums'=>$sendnums-$acquirednum-1,'acquirednum'=>$acquirednum+1), '成功领取');

    }

    public function randFloat($min = 0, $max = 1) {
        $rand = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return floatval(number_format($rand,2));
    }
    
    public function redPacket(Request $request)
    {
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $data = $request->all();
        $start = $end = '';
        if (isset($data['time'])) {
            list($start, $end) = [$data['time'][0], $data['time'][1]];
        }

        $list = Userredpacket::where('uid', $user->id)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->orderBy('id', 'desc')->paginate(10);

        foreach ($list as $k => $v) {
            $list[$k]['amount'] = $v['redpacketmoney'];
        }

        return $this->returnMsg(200, $list);
    }
    
    public function getRedPacket(Request $request)
    {
        if ($request->isMethod('post')) {
            $token = $request->header('authorization');
            $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 
            $data = $request->all();
            $id = $data['id'];
            try {
                if ($id > 0) {
                    $userredpacket = Userredpacket::where('uid', $user->id)->where('id', $id)->lockForUpdate()->first();
                    if ($userredpacket && !$userredpacket->status) {
                        $userinfo = User::where('id', $user->id)->lockForUpdate()->first();
                        $userinfo->balance = $userinfo->balance + $userredpacket->redpacketmoney;
                        $userinfo->save();
                        $userredpacket->status = 1;
                        $userredpacket->usetime = date('Y-m-d H:i:s');
                        $userredpacket->save();
                        $arr = [
                            'order_no' => date('Ymd') . '_' . $userinfo->id . '_' . time(),
                            'api_type' => 'web',
                            'user_id' => $userinfo->id,
                            'transfer_type' => 5,
                            'money' => $userredpacket->redpacketmoney,
                            'cash_fee' => 0,
                            'real_money' => $userredpacket->redpacketmoney,
                            'before_money' => $userinfo->balance - $userredpacket->redpacketmoney,
                            'after_money' => $userinfo->balance,
                            'state' => 1
                        ];
                        TransferLog::create($arr);
                        /*                    $Gamereport = new GamereportService();
                                            $datae['uid'] = $userinfo->id;
                                            $datae['pid'] = $userinfo->pid;
                                            $datae['isagent'] = $userinfo->isagent;
                                            $datae['redpackectnum'] = 1;
                                            $datae['totalredpackect'] = $userredpacket->redpacketmoney;
                                            $Gamereport->add($datae);*/
                        return $this->returnMsg(200, '', '成功领取');
                    }
                }else{

                    $userfanshui = Userredpacket::where('uid', $user->id)->where('state',0)->lockForUpdate()->sum('redpacketmoney');
                    if ($userfanshui) {
                        $userinfo = User::where('id', $user->id)->lockForUpdate()->first();
                        $userinfo->balance = $userinfo->balance + $userfanshui;
                        $userinfo->save();

                        Userredpacket::where('uid', $user->id)
                            ->where('state',0)
                            ->update(['state' => 1,'usetime'=>date('Y-m-d H:i:s')]);

                        /*
                                                $Gamereport = new GamereportService();
                                                $datae['uid'] = $userinfo->id;
                                                $datae['pid'] = $userinfo->pid;
                                                $datae['isagent'] = $userinfo->isagent;
                                                $datae['releasewater'] = 1;
                                                $datae['totalredpackect'] = $userfanshui;
                                                $Gamereport->add($datae);*/

                        return $this->returnMsg(200, '', '成功领取');
                    }else{
                        return $this->returnMsg(202, '', '没有可领取的返水');
                    }

                }
            } catch (\Exception $e) {
                return $this->returnMsg(202, '', '领取失败');
            }

        }
    }
    
    
}
