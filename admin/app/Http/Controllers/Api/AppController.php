<?php
//decode by http://www.yunlu99.com/
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityApply;
use App\Models\ActivityType;
use App\Models\Bank;
use App\Models\Api;
use App\Models\User_Api;
use App\Models\Message;
use App\Models\UserMessage;
use App\Models\PaySetting;
use App\Models\SystemConfig;
use App\Models\UserCard;
use App\Models\User;
use App\Models\Users;
use App\Models\Usersmoney;
use App\Services\TgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransferLog;
use App\Models\Recharge;
use App\Models\Withdraw;
use App\Models\Article;
use App\Models\UserVip;
use App\Models\Banner;
use App\Models\CodePay;
use App\Models\GameRecord;
use App\Models\AgentApply;
use App\Models\GameList;
use App\Models\GameListApp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use QrCode;
use Illuminate\Support\Facades\File;
use App\Services\PayService;
class AppController extends Controller
{
    protected $messages = [
        'password.required' => '密码不能为空',
        'password.min' => '密码6到12位',
		'password.max' => '密码6到12位',
        'name.required' => '账号不能为空',
        'qk_pwd.required' => '取款密码不能为空',
        'qk_pwd.min' => '取款密码6到12位',
		'qk_pwd.max' => '取款密码6到12位',
        'realname.required' => '真实姓名不能为空',		
    ];

    public function __construct()
    {
        $this->PayService = new PayService();
    }
    public function pay_list()
    {
		$data = array();
		$data['bank'] = 0;
		$data['alipay'] = 0;
		$data['weixin'] = 0;
		$data['usdt_trc20'] = 0;
		$data['usdt_erc20'] = 0;
		$data['cgpay'] = 0;
		$data['zxcgpay'] = 0;
		$PaySetting = PaySetting::where('state',1)->first();
		if($PaySetting){
			$data['bank'] = 1;
		}
		$CodePay = CodePay::where('status',1)->get()->toArray();
		foreach($CodePay as $key => $value){
			if (strstr( $value['content'] , '支付宝' ) !== false ){
                $data['alipay'] = 1;
			}
			if (strstr( $value['content'] , '微信' ) !== false ){
                $data['weixin'] = 1;
			}
			if (strstr( $value['content'] , 'TRC20' ) !== false ){
                $data['usdt_trc20'] = 1;
			}
			if (strstr( $value['content'] , 'ERC20' ) !== false ){
                $data['usdt_erc20'] = 1;
			}
			if (strstr( $value['content'] , 'CGPay转账' ) !== false ){
                $data['cgpay'] = 1;
			}
			if (strstr( $value['content'] , 'CGPay在线' ) !== false ){
                $data['zxcgpay'] = 1;
			}			
		}
		return $this->returnMsg(200,$data,'成功');
    }	
    public function open($code)
    {
		if($code == 'wechat'){
			$url = 'weixin://';			
		}else if($code == 'alipay'){
			$url = 'alipays://';	
		}else{
			$url = 'https://www.baidu.com/';
		}
        header("Location:".$url);
		die();
    }	
    public function zxcgpay_pay(Request $request)
    {

        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if($data['money'] == ''){
			return $this->returnMsg(201,'','请填写完整数据');
		}
		//$data['pay_id']固定参数：zxcgpay
		if($data['pay_id'] == ''){
			return $this->returnMsg(201,'','通道未开启，请切换其他充值方式');
		}		
		$CodePay = CodePay::where('status',1)->where('content','like','%CGPAY在线%')->first();
		if(!$CodePay){
			return $this->returnMsg(201,'','入款方式不存在,请重试或联系客服');
		}
		$money = (float)$data['money'];
		$cz_min = $CodePay->min_price;
		$cz_max = $CodePay->max_price;
		if($money < $cz_min || $money > $cz_max){
			return $this->returnMsg(201,'','单笔充值金额限制'.$cz_min.'元-'.$cz_max.'元');
		}	
        $out_trade_no = time().$user->id.rand(1000,9999);
        $datas['out_trade_no'] = $out_trade_no;
        $datas['user_id'] = $user->id;		
		$datas['amount'] = $money;
		$datas['pay_way'] = $CodePay->id;
		$datas['cash_fee'] = 0;
		$datas['real_money'] = $datas['amount'];
		$datas['usdt_rate'] = 0;
		$datas['state'] = 1;
		$pay = $this->PayService->cgpay($datas['out_trade_no'],$datas['amount']);
		if(!$pay){
			return $this->returnMsg(201,'','网络错误');
		}
		$pay = json_decode($pay,true);
		if(!is_array($pay)){
			return $this->returnMsg(201,'','数据解析失败');
		}
		if($pay['ReturnCode'] > 0){
			return $this->returnMsg(201,'',$pay['ReturnMessage']);
		}
		$res = Recharge::create($datas);
        return $this->returnMsg(200,$pay['Qrcode'],'成功');		
	}	
    public function wechat_pay(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if($data['money'] == '' || $data['name'] == ''){
			return $this->returnMsg(201,'','请填写完整数据');
		}
		if($data['pay_id'] == ''){
			return $this->returnMsg(201,'','通道未开启，请切换其他充值方式');
		}		
		$CodePay = CodePay::where('status',1)->where('id',$data['pay_id'])->first();
		if(!$CodePay){
			return $this->returnMsg(201,'','入款方式不存在,请重试或联系客服');
		}
		$money = (float)$data['money'];
		$cz_min = $CodePay->min_price;
		$cz_max = $CodePay->max_price;
		if($money < $cz_min || $money > $cz_max){
			return $this->returnMsg(201,'','微信单笔充值金额限制'.$cz_min.'元-'.$cz_max.'元');
		}	
        $out_trade_no = time().$user->id.rand(1000,9999);
        $datas['out_trade_no'] = $out_trade_no;
        $datas['user_id'] = $user->id;		
		$datas['amount'] = $money;
		$datas['pay_way'] = $CodePay->id;
		$datas['bank_owner'] = $data['name'];
		$datas['cash_fee'] = 0;
		$datas['real_money'] = $datas['amount'];
		$datas['usdt_rate'] = 0;
		$datas['state'] = 1;
		
		$res = Recharge::create($datas);
        return $this->returnMsg(200,'','充值成功,请等待审核');		
	}	
    public function usdt_pay(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if($data['money'] == ''){
			return $this->returnMsg(201,'','请填写完整数据');
		}
		if($data['pay_id'] == ''){
			return $this->returnMsg(201,'','通道未开启，请切换其他充值方式');
		}		
		$CodePay = CodePay::where('status',1)->where('id',$data['pay_id'])->first();
		if(!$CodePay){
			return $this->returnMsg(201,'','入款方式不存在,请重试或联系客服');
		}
		$money = (float)$data['money'];
		$cz_min = $CodePay->min_price;
		$cz_max = $CodePay->max_price;
		if($money < $cz_min || $money > $cz_max){
			return $this->returnMsg(201,'','单笔充值金额限制'.$cz_min.'元-'.$cz_max.'元');
		}	
        $out_trade_no = time().$user->id.rand(1000,9999);
        $datas['out_trade_no'] = $out_trade_no;
        $datas['user_id'] = $user->id;		
		$datas['amount'] = $money;
		$datas['pay_way'] = $CodePay->id;
		$datas['cash_fee'] = 0;
		$datas['real_money'] = $datas['amount'];
		$datas['usdt_rate'] = SystemConfig::getValue('usdt_rate');  //USDT汇率
		$datas['state'] = 1;
		
		$res = Recharge::create($datas);
        return $this->returnMsg(200,'','充值成功,请等待审核');		
	}
	
	/**
	 * TRON USDT充值接口
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function tron_usdt_pay(Request $request)
	{
		$data = $request->all();
		$token = $request->header('authorization');
		if (empty($token) && isset($data['lastsession'])) { $token = $data['lastsession']; }
		$token = is_string($token) ? str_replace('Bearer ', '', $token) : '';
		$user = User::where('api_token', $token)->first();
		
		if (!$user) {
			return $this->returnMsg(201, '', '登录信息已过期');
		}
		
		if (empty($data['money'])) {
			return $this->returnMsg(201, '', '请填写充值金额');
		}
		
		$money = (float)$data['money'];
		$protocol = strtoupper($data['protocol'] ?? 'TRC20');
		
		// 检查金额限制
		$minAmount = SystemConfig::getValue('tron_min_amount', 10);
		$maxAmount = SystemConfig::getValue('tron_max_amount', 50000);
		
		if ($money < $minAmount || $money > $maxAmount) {
			return $this->returnMsg(201, '', "充值金额限制{$minAmount}-{$maxAmount}元");
		}
		
		try {
			// 仅TRON模式：若未配置TRON参数则直接报错
			$tronUsdtAddress = SystemConfig::getValue('tron_usdt_address');
			$tronApiKey = SystemConfig::getValue('tron_api_key');
			if (empty($tronUsdtAddress) || empty($tronApiKey)) {
				return $this->returnMsg(201, '', 'TRON USDT通道未配置，请联系管理员');
			}

			// 使用TRON服务生成地址（可带自定义汇率）
			$tronService = new \App\Services\TronUsdtService();
			$customRate = SystemConfig::getValue('tron_exchange_rate');
			$rechargeInfo = $tronService->generateRechargeInfo($money, $user->id, $customRate);
			if (!$rechargeInfo['success']) {
				return $this->returnMsg(201, '', $rechargeInfo['message']);
			}
			$datas = [
				'out_trade_no' => $rechargeInfo['data']['out_trade_no'],
				'user_id' => $user->id,
				'amount' => $money,
				'pay_way' => 5,
				'cash_fee' => 0,
				// 统一以三位小数的最终USDT金额入库
				'real_money' => $rechargeInfo['data']['usdt_amount'],
				'usdt_rate' => $rechargeInfo['data']['exchange_rate'],
				'state' => 1,
				'tron_usdt_amount' => $rechargeInfo['data']['usdt_amount'],
				'tron_address' => $rechargeInfo['data']['tron_address'],
				'tron_network' => $protocol,
				// 备注展示随机小数与最终USDT金额，确保和订单金额一致
				'info' => $protocol . ' USDT充值，最终USDT金额 ' . number_format($rechargeInfo['data']['usdt_amount'], 3, '.', '')
			];
			$res = Recharge::create($datas);
			if ($res) {
				return $this->returnMsg(200, [
					'out_trade_no' => $rechargeInfo['data']['out_trade_no'],
					'usdt_amount' => $rechargeInfo['data']['usdt_amount'],
					'tron_address' => $rechargeInfo['data']['tron_address'],
					'exchange_rate' => $rechargeInfo['data']['exchange_rate'],
					'random_decimal' => $rechargeInfo['data']['random_decimal']
				], '充值订单创建成功，请转账到指定地址');
			}
			return $this->returnMsg(201, '', '创建充值订单失败');
			
		} catch (\Exception $e) {
			\Log::error('TRON USDT充值失败:', [
				'error' => $e->getMessage(),
				'user_id' => $user->id,
				'amount' => $money
			]);
			
			return $this->returnMsg(201, '', '系统错误，请稍后重试');
		}
	}
	
	/**
	 * 提交TRON交易哈希
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function submit_tron_hash(Request $request)
	{
		$data = $request->all();
		$token = $request->header('authorization');
		if (empty($token) && isset($data['lastsession'])) { $token = $data['lastsession']; }
		$token = is_string($token) ? str_replace('Bearer ', '', $token) : '';
		$user = User::where('api_token', $token)->first();
		
		if (!$user) {
			return $this->returnMsg(201, '', '登录信息已过期');
		}
		
		if (empty($data['out_trade_no'])) {
			return $this->returnMsg(201, '', '请提供订单号');
		}
		
		if (empty($data['tx_hash'])) {
			return $this->returnMsg(201, '', '请提供交易哈希');
		}
		
		// 已完成订单禁止重复提交哈希
		$recharge = \App\Models\Recharge::where('out_trade_no', $data['out_trade_no'])
			->where('user_id', $user->id)
			->first();
		if (!$recharge) {
			return $this->returnMsg(201, '', '订单不存在');
		}
		if ((int)$recharge->state !== 1) {
			return $this->returnMsg(201, '', '订单已处理，不能重复提交哈希');
		}

		try {
			// 使用TRON USDT服务验证交易
			$tronService = new \App\Services\TronUsdtService();
			$verifyResult = $tronService->verifyTransaction($data['tx_hash'], $data['out_trade_no']);
			
			if ($verifyResult['success']) {
				return $this->returnMsg(200, $verifyResult['data'], $verifyResult['message']);
			} else {
				return $this->returnMsg(201, '', $verifyResult['message']);
			}
			
		} catch (\Exception $e) {
			\Log::error('提交TRON哈希失败:', [
				'error' => $e->getMessage(),
				'user_id' => $user->id,
				'out_trade_no' => $data['out_trade_no'],
				'tx_hash' => $data['tx_hash']
			]);
			
			return $this->returnMsg(201, '', '验证失败，请稍后重试');
		}
	}
	
	/**
	 * 生成TRON USDT地址二维码
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function generate_tron_qrcode(Request $request)
	{
		$data = $request->all();
		$token = $request->header('authorization');
		if (empty($token) && isset($data['lastsession'])) { $token = $data['lastsession']; }
		$token = is_string($token) ? str_replace('Bearer ', '', $token) : '';
		$user = User::where('api_token', $token)->first();
		
		if (!$user) {
			return $this->returnMsg(201, '', '登录信息已过期');
		}
		
		$address = $data['address'] ?? '';
		if (empty($address)) {
			return $this->returnMsg(201, '', '请提供地址');
		}
		
		try {
			// 使用Simple QR Code库生成二维码
			$qrCode = \QrCode::format('png')
				->size(300)
				->margin(10)
				->errorCorrection('H')
				->generate($address);
			
			// 将二维码转换为base64编码
			$base64 = 'data:image/png;base64,' . base64_encode($qrCode);
			
			return $this->returnMsg(200, [
				'qrcode' => $base64,
				'address' => $address,
				'size' => 300
			], '二维码生成成功');
			
		} catch (\Exception $e) {
			\Log::error('生成TRON地址二维码失败:', [
				'error' => $e->getMessage(),
				'user_id' => $user->id,
				'address' => $address
			]);
			
			return $this->returnMsg(201, '', '生成二维码失败，请稍后重试');
		}
	}

	/**
	 * 获取TRON USDT充值状态
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function tron_recharge_status(Request $request)
	{
		$data = $request->all();
		$token = $request->header('authorization');
		if (empty($token) && isset($data['lastsession'])) { $token = $data['lastsession']; }
		$token = is_string($token) ? str_replace('Bearer ', '', $token) : '';
		$user = User::where('api_token', $token)->first();
		
		if (!$user) {
			return $this->returnMsg(201, '', '登录信息已过期');
		}
		
		if (empty($data['out_trade_no'])) {
			return $this->returnMsg(201, '', '请提供订单号');
		}
		
		$recharge = Recharge::where('out_trade_no', $data['out_trade_no'])
			->where('user_id', $user->id)
			->first();
			
		if (!$recharge) {
			return $this->returnMsg(201, '', '订单不存在');
		}
		
		$status = [
			'out_trade_no' => $recharge->out_trade_no,
			'amount' => $recharge->amount,
			'usdt_amount' => $recharge->tron_usdt_amount,
			'state' => $recharge->state,
			'state_text' => $this->getRechargeStateText($recharge->state),
			'tron_address' => $recharge->tron_address,
			'tron_tx_hash' => $recharge->tron_tx_hash,
			'tron_confirmations' => $recharge->tron_confirmations,
			'created_at' => $recharge->created_at,
			'updated_at' => $recharge->updated_at
		];
		
		return $this->returnMsg(200, $status, '获取成功');
	}
	
	/**
	 * 获取充值状态文本
	 * 
	 * @param int $state
	 * @return string
	 */
	private function getRechargeStateText($state)
	{
		switch ($state) {
			case 1:
				return '待支付';
			case 2:
				return '已支付';
			case 3:
				return '已拒绝';
			default:
				return '未知状态';
		}
	}
    public function cgpay_pay(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if($data['money'] == ''){
			return $this->returnMsg(201,'','请填写完整数据');
		}
		if($data['pay_id'] == ''){
			return $this->returnMsg(201,'','通道未开启，请切换其他充值方式');
		}		
		$CodePay = CodePay::where('status',1)->where('id',$data['pay_id'])->first();
		if(!$CodePay){
			return $this->returnMsg(201,'','入款方式不存在,请重试或联系客服');
		}
		$money = (float)$data['money'];
		$cz_min = $CodePay->min_price;
		$cz_max = $CodePay->max_price;
		if($money < $cz_min || $money > $cz_max){
			return $this->returnMsg(201,'','单笔充值金额限制'.$cz_min.'元-'.$cz_max.'元');
		}	
        $out_trade_no = time().$user->id.rand(1000,9999);
        $datas['out_trade_no'] = $out_trade_no;
        $datas['user_id'] = $user->id;		
		$datas['amount'] = $money;
		$datas['pay_way'] = $CodePay->id;
		$datas['cash_fee'] = 0;
		$datas['real_money'] = $datas['amount'];
		$datas['usdt_rate'] = 1;
		$datas['state'] = 1;
		
		$res = Recharge::create($datas);
        return $this->returnMsg(200,'','充值成功,请等待审核');		
	}	
    public function wechat_info(Request $request)
    {
		$data = array();
		$data['pay_id'] = '';
		$data['bank_name'] = '';
		$data['account'] = '';
		$data['qrcodeurl'] = '';
		$CodePay = CodePay::where('status',1)->where('content','like','%微信%')->first();
		if($CodePay){
			$data['pay_id'] = $CodePay->id;
			$data['bank_name'] = $CodePay->content;
			$data['account'] = $CodePay->mch_id;
			$data['qrcodeurl'] = env('APP_URL').'/uploads/'.$CodePay->payimg;		
		}
		return $this->returnMsg(200,$data,'成功');
	}

    public function cgpay_info(Request $request)
    {
		$data = array();
		$data['pay_id'] = '';
		$data['bank_name'] = '';
		$data['account'] = '';
		$data['qrcodeurl'] = '';
		$CodePay = CodePay::where('status',1)->where('content','like','%CGPay转账%')->first();
		if($CodePay){
			$data['pay_id'] = $CodePay->id;
			$data['bank_name'] = $CodePay->content;
			$data['account'] = $CodePay->mch_id;
			$data['qrcodeurl'] = env('APP_URL').'/uploads/'.$CodePay->payimg;		
		}
		return $this->returnMsg(200,$data,'成功');
	}
	
    public function usdt_info(Request $request)
    {
		$post = $request->all();
		if($post['paytype'] == ''){   //TRC20   ERC20
			return $this->returnMsg(201,'','通道不存在或已维护');
		}
		$usdt_rate = SystemConfig::getValue('usdt_rate');  //USDT汇率
		$where = 'USDT-'.$post['paytype'];
		$data = array();
		$data['pay_id'] = '';
		$data['bank_name'] = '';
		$data['account'] = '';
		$data['qrcodeurl'] = '';
		$data['usdt_rate'] = $usdt_rate;
		$CodePay = CodePay::where('status',1)->where('content',$where)->first();
		if($CodePay){
			$data['pay_id'] = $CodePay->id;
			$data['bank_name'] = $CodePay->content;
			$data['account'] = $CodePay->mch_id;
			$data['qrcodeurl'] = env('APP_URL').'/uploads/'.$CodePay->payimg;		
		}
		return $this->returnMsg(200,$data,'成功');
	}
	
    public function alipay_pay(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if($data['money'] == '' || $data['name'] == ''){
			return $this->returnMsg(201,'','请填写完整数据');
		}
		if($data['pay_id'] == ''){
			return $this->returnMsg(201,'','通道未开启，请切换其他充值方式');
		}		
		$CodePay = CodePay::where('status',1)->where('id',$data['pay_id'])->first();
		if(!$CodePay){
			return $this->returnMsg(201,'','入款方式不存在,请重试或联系客服');
		}
		$money = (float)$data['money'];
		$cz_min = $CodePay->min_price;
		$cz_max = $CodePay->max_price;
		if($money < $cz_min || $money > $cz_max){
			return $this->returnMsg(201,'','支付宝单笔充值金额限制'.$cz_min.'元-'.$cz_max.'元');
		}	
        $out_trade_no = time().$user->id.rand(1000,9999);
        $datas['out_trade_no'] = $out_trade_no;
        $datas['user_id'] = $user->id;		
		$datas['amount'] = $money;
		$datas['pay_way'] = $CodePay->id;
		$datas['bank_owner'] = $data['name'];
		$datas['cash_fee'] = 0;
		$datas['real_money'] = $datas['amount'];
		$datas['usdt_rate'] = 0;
		$datas['state'] = 1;
		
		$res = Recharge::create($datas);
        return $this->returnMsg(200,'','充值成功,请等待审核');		
	}	
    public function alipay_info(Request $request)
    {
		$data = array();
		$data['pay_id'] = '';
		$data['bank_name'] = '';
		$data['account'] = '';
		$data['qrcodeurl'] = '';
		$CodePay = CodePay::where('status',1)->where('content','like','%支付宝%')->first();
		if($CodePay){
			$data['pay_id'] = $CodePay->id;
			$data['bank_name'] = $CodePay->content;
			$data['account'] = $CodePay->mch_id;
			$data['qrcodeurl'] = env('APP_URL').'/uploads/'.$CodePay->payimg;		
		}
		return $this->returnMsg(200,$data,'成功');
	}	
    public function recharge_list(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
        $page = $data['page'];
        $start_at = date('Y-m-d H:i:s', time() - 60 * 1440 * 30);
		$end_at = date('Y-m-d H:i:s');
		$Recharge = Recharge::where("user_id",$user->id)->select("amount","state","created_at")->whereBetween('created_at', [$start_at, $end_at])->orderBy('id', 'desc')->paginate(20,['*'],'page',$page)->toArray();
		$Recharge = $Recharge['data'];
		foreach($Recharge as $key => $value){
			if($value['state'] == 1){
				$Recharge[$key]['state'] = '审核中';
			}
			if($value['state'] == 2){
				$Recharge[$key]['state'] = '<font color="green">已通过</font>';
			}
			if($value['state'] == 3){
				$Recharge[$key]['state'] = '<font color="red">未通过</font>';
			}			
		}
		return $this->returnMsg(200,$Recharge,'成功');
	}
    public function bank_pay(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if($data['money'] == '' || $data['name'] == ''){
			return $this->returnMsg(201,'','请填写完整数据');
		}
		if($data['pay_id'] == ''){
			return $this->returnMsg(201,'','通道未开启，请切换其他充值方式');
		}		
		$PaySetting = PaySetting::where('state',1)->where('id',$data['pay_id'])->first();
		if(!$PaySetting){
			return $this->returnMsg(201,'','入款卡号不存在,请重试或联系客服');
		}
		$money = (float)$data['money'];
		$cz_min = SystemConfig::getValue('min_price');
		$cz_max = SystemConfig::getValue('max_price');
		if($money < $cz_min || $money > $cz_max){
			return $this->returnMsg(201,'','银行卡单笔充值金额限制'.$cz_min.'元-'.$cz_max.'元');
		}	
        $out_trade_no = time().$user->id.rand(1000,9999);
        $datas['out_trade_no'] = $out_trade_no;
        $datas['user_id'] = $user->id;		
		$datas['amount'] = $money;
		$datas['pay_way'] = $PaySetting->id;
		$datas['bank_owner'] = $data['name'];
		$datas['cash_fee'] = 0;
		$datas['real_money'] = $datas['amount'];
		$datas['usdt_rate'] = 0;
		$datas['state'] = 1;
		
		$res = Recharge::create($datas);
        return $this->returnMsg(200,'','充值成功,请等待审核');		
	}
	
    public function getbank_info(Request $request)
    {
		$data = array();
		$data['pay_id'] = '';
		$data['bank_name'] = '';
		$data['username'] = '';
		$data['card_no'] = '';
		$data['bank_address'] = '';
		$PaySetting = PaySetting::where('state',1)->first();
		if($PaySetting){
			$Bank = Bank::where('id',$PaySetting->bank_id)->first();
			if($Bank){
				$data['pay_id'] = $PaySetting->id;
				$data['bank_name'] = $Bank->bank_name;
				$data['username'] = $PaySetting->bank_owner;
				$data['card_no'] = $PaySetting->bank_no;
				$data['bank_address'] = $PaySetting->bank_address;		
			}
		}
		return $this->returnMsg(200,$data,'成功');
	}
	
    public function cash_list(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
        $page = $data['page'];
        $start_at = date('Y-m-d H:i:s', time() - 60 * 1440 * 30);
		$end_at = date('Y-m-d H:i:s');
		$Withdraw = Withdraw::where("user_id",$user->id)->select("amount","state","created_at")->whereBetween('created_at', [$start_at, $end_at])->orderBy('id', 'desc')->paginate(20,['*'],'page',$page)->toArray();
		$Withdraw = $Withdraw['data'];
		foreach($Withdraw as $key => $value){
			if($value['state'] == 1){
				$Withdraw[$key]['state'] = '审核中';
			}
			if($value['state'] == 2){
				$Withdraw[$key]['state'] = '<font color="green">已通过</font>';
			}
			if($value['state'] == 3){
				$Withdraw[$key]['state'] = '<font color="red">未通过</font>';
			}			
		}
		return $this->returnMsg(200,$Withdraw,'成功');
	}
	
    public function post_drawing(Request $request)
    {
		$data = $request->all();
		$user = User::where('api_token',$data['lastsession'])->first(); 
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		if(!is_numeric($data['money'])){
			return $this->returnMsg(201,'','金额输入错误');
		}
		if(!$data['bankid']){
			return $this->returnMsg(201,'','请选择提款方式');
		}		
		$tk_min = SystemConfig::getValue('min_withdraw_money');
		$tk_max = SystemConfig::getValue('max_withdraw_money');
		if($data['money'] < $tk_min || $data['money'] > $tk_max){
			return $this->returnMsg(201,'','单笔提款金额限制'.$tk_min.'元-'.$tk_max.'元');
		}		

		$tg = new TgService;
		$TransferLog = TransferLog::where('transfer_type', 0)->where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if($TransferLog && $TransferLog->api_type){			
			$result = $tg->balance($TransferLog->api_type,$user->username);
			if($result['code'] != 200){
				return $result;
			}
			$api_money = $result['data'];
			if($api_money >= '1'){
				$api_money = intval($api_money);
				$client_transfer_id = time() . $user->id . rand(100000, 999999);
				$arr = [
					'order_no' => $client_transfer_id,
					'api_type' => $TransferLog->api_type,
					'user_id' => $user->id,
					'transfer_type' => 1,
					'money' => $api_money,
					'cash_fee' => 0,
					'real_money' => $api_money,
					'before_money' => $user->balance ,
					'after_money' => $user->balance + $api_money,
					'state' => 2
				];
				$Transfers_id = TransferLog::create($arr);

				$res = $tg->withdrawal($user->username, $api_money, $client_transfer_id, $TransferLog->api_type);
				if($res['code'] != 200){
					$Transfers_id->delete();
					return $res;
				}
				$user->increment('balance', $api_money);
				$transferlog = TransferLog::where('order_no', $client_transfer_id)->first();
				$transferlog->state = 1;
				$transferlog->save();
				$user_api = User_Api::where('api_code', $TransferLog->api_type)->where('user_id', $user->id)->where('api_user', $user->username)->first();
				if($user_api->api_money <= $api_money){
					$user_api->api_money = 0;
					$user_api->save();						
				}else{
					$user_api->api_money -= $api_money;
					$user_api->save();						
				}
			}
		}
		
		if($user->balance < $data['money']){
			return $this->returnMsg(201,'','余额不足');
		}
		$card = UserCard::find($data['bankid']);
		if(!$card){
			return $this->returnMsg(201,'','提款方式不存在');
		}
		$user->decrement('balance',$data['money']);
		$order_no = time().rand(1000,9999);
		$type = 1;
		if($card['bank_owner'] == 'TRC20'){
			$type = 2;
		}
		if($card['bank_owner'] == 'ERC20'){
			$type = 3;
		}		
        $item = [
            'order_no' => $order_no,
            'type' => $type,
            'card_id' => $data['bankid'],
            'user_id' => $user->id,
            'amount' => $data['money'],
            'cash_fee' => 0,
            'real_money' => $data['money'],
            'usdt_rate' => ($type == 1) ? 0 : SystemConfig::getValue('withdraw_usdt_rate')
        ];
        $res = Withdraw::create($item);
        return $this->returnMsg($res ? 200 : 500,'',$res ? '提款成功' : '提款失败，请联系客服');		
	}
    public function post_update_bank_info(Request $request)
    {
		$data = $request->all();
		if($data['bank_name'] == ''){
			return $this->returnMsg(201,'','请选择银行');
		}
		if($data['bank_address'] == ''){
			return $this->returnMsg(201,'','请输入开户地址');
		}
		if($data['bank_username'] == ''){
			return $this->returnMsg(201,'','请输入持卡人姓名');
		}
		if($data['bank_card'] == ''){
			return $this->returnMsg(201,'','请输入银行卡账号');
		}
		$user = User::where('api_token',$data['lastsession'])->first(); 
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}		
		$UserCard = UserCard::where('user_id',$user->id)->count();
		if($UserCard >= 5){
			return $this->returnMsg(201,'','每个会员最多可绑定5张卡');
		}
		if($data['bank_name'] == 98 || $data['bank_name'] == 99){
			$Bank['user_id'] = $user->id;
			$Bank['bank'] = 'USDT';
			$Bank['bank_no'] = $data['bank_card'];
			$Bank['bank_address'] = $data['bank_address'];
			$Bank['bank_owner'] = $data['bank_name'] == 98 ? 'ERC20' : 'TRC20';
			UserCard::create($Bank);
			return $this->returnMsg(200,$Bank,'成功');
		}
        $Bank = Bank::where('id',$data['bank_name'])->first();
        if(!$Bank){
			return $this->returnMsg(201,'','银行不存在');
		}	
		$Bankdata['user_id'] = $user->id;
		$Bankdata['bank'] = $Bank->bank_name;
		$Bankdata['bank_no'] = $data['bank_card'];
		$Bankdata['bank_address'] = $data['bank_address'];
		$Bankdata['bank_owner'] = $data['bank_username'];
		UserCard::create($Bankdata);		
		return $this->returnMsg(200,$Bankdata,'成功');
	}
	
    public function bindbanklist(Request $request)
    {
        $Bank = Bank::where('state',1)->select("id","bank_name")->orderBy('id','desc')->get()->toArray();
        
		$trc20 = array(
			    'id' => 99,
				'bank_name' => 'USDT-TRC20'
			);
		$erc20 = array(
			    'id' => 98,
				'bank_name' => 'USDT-ERC20'
			);			

		array_push($Bank,$trc20,$erc20);
		return $this->returnMsg(200,$Bank,'成功');
	}
	
    public function activitiesgo(Request $request)
    {
		$data = $request->all();
		$user = User::where('api_token',$data['lastsession'])->first(); 
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		$activity = Activity::where('id', $data['activityid'])->first();
		if(empty($activity)){
			return $this->returnMsg(202, '', '活动不存在');
		}

		$isapple = ActivityApply::where("user_id",$user->id)->where('activity_id',$data['activityid'])->first();
		if($isapple){
			if($isapple->state==1){
				return $this->returnMsg(202, '', '您已经申请过该活动，等待管理员审核');
			}
			if($isapple->state==2){
				return $this->returnMsg(202, '', '您已经申请过，已审核通过');
			}
			if($isapple->state==3){
				return $this->returnMsg(202, '', '您已经申请过，审核未通过');
			}
		}

		$arr['activity_id'] = $data['activityid'];
		$arr['user_id'] = $user->id;
		$arr['state'] = 1;
		$arr['created_at'] = time();
		$arr['updated_at'] = time();
		if(ActivityApply::create($arr)){
			return $this->returnMsg(200, '', '申请成功');
		}else{
			return $this->returnMsg(202, '', '申请失败');
		}		
	}	
    public function activities(Request $request)
    {
        $Activity = Activity::where('app_state',1)->select("app_img","type","id","can_apply","content","memo")->get()->toArray();
		foreach($Activity as $key => $value){
			$Activity[$key]['app_img'] = env('APP_URL').'/uploads/'.$value['app_img'];
			//活动类型：1真人,2捕鱼,3电子,4彩票,5体育,6棋牌,7电竞，99综合活动
			if($value['type'] == 5){
				$Activity[$key]['type'] = 6;
			}else if($value['type'] == 6){
				$Activity[$key]['type'] = 3;
			}else if($value['type'] == 7){
				$Activity[$key]['type'] = 4;
			}else if($value['type'] == 8){
				$Activity[$key]['type'] = 1;
			}else if($value['type'] == 10){
				$Activity[$key]['type'] = 5;
			}else if($value['type'] == 11){
				$Activity[$key]['type'] = 2;
			}else if($value['type'] == 12){
				$Activity[$key]['type'] = 7;
			}else{
				$Activity[$key]['type'] = 99;
			}			
		}

		return $this->returnMsg(200,$Activity,'成功');
	}	
	//游戏类型：1真人,2捕鱼,3电子,4彩票,5体育,6棋牌,7电竞
    public function usergameForm(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
        $game_type = $data['gameType'];
		$timeType = $data['timeType'];
		$time = time();
		$start_at = date('Y-m-d 00:00:00');
		$end_at = date('Y-m-d H:i:s');
		if($timeType == 2){
			$start_at = date('Y-m-d 00:00:00',$time - 86400);
			$end_at = date('Y-m-d 23:59:59',$time - 86400);
		}
		if($timeType == 3){
			$start_at = date('Y-m-01 00:00:00');
			$end_at = date('Y-m-d H:i:s');
		}		
		$where = array();
		$where['user_id'] = $user->id;
		$where['game_type'] = $game_type;
		$where['status'] = 1;
		$Game_Record = GameRecord::where($where)->whereBetween('created_at', [$start_at, $end_at])->first( array( \DB::raw('SUM(valid_amount) as valid_amount'), \DB::raw('SUM(win_loss) as win_loss') ) )->toArray();
        return response()->json([
            'code'    => 200,
            'message' => '成功',
            'data'    => '',
			'total_betAmount' => $Game_Record['valid_amount'] ? $Game_Record['valid_amount'] : '0.00',
			'total_validBetAmount' => $Game_Record['win_loss'] ? $Game_Record['win_loss'] : '0.00'
        ]);
	}	
    public function gameRecordList(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
        $page = $data['page'];
        $start_at = date('Y-m-d H:i:s', time() - 60 * 1440 * 30);
		$end_at = date('Y-m-d H:i:s');
		$Game_Record = GameRecord::where("user_id",$user->id)->select("platform_type as api_name","game_code as gameCode","bet_amount as betAmount","win_loss as netAmount","bet_time as betTime")->whereBetween('created_at', [$start_at, $end_at])->orderBy('id', 'desc')->paginate(20,['*'],'page',$page)->toArray();
		return $this->returnMsg(200,$Game_Record,'成功');
	}	
    public function register(Request $request)
    {
        $rules = [
    		'name' => 'required',
            'password' => 'required|min:6|max:12',
			'qk_pwd' => 'required|min:6|max:12',
			'realname' => 'required',
    	];
        $this->validate($request,$rules,$this->messages);
		$data = $request->all();
        $user = User::where('username',$data['name'])->first();
        if ($user) return $this->returnMsg(202,'','会员已存在');
        $arr = [
            'username' => $data['name'],
            'realname' => $data['realname'],
            'password' => Hash::make($data['password']),
            'paypwd' =>Hash::make($data['qk_pwd']),
            'status' => 1,
            'vip' => 1,
            'api_token' => Str::random(60),
            'pid' => $data['pid'] ?? 0
        ];
        $res = User::create($arr);
		return $this->returnMsg($res ? 200 : 500,'','成功');
	}	
    public function islogin(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		$UserCard = UserCard::where('user_id',$user->id)->select("id","bank as bank_name","bank_no as bank_card","bank_address","bank_owner as bank_username")->orderBy('id','desc')->get()->toArray();
		$userdata = array();
		$userdata['last_session'] = $user->api_token;  //会员登陆token
		$userdata['id'] = $user->id;   //会员id
		$userdata['agent'] = $user->isagent;   //是否代理
		$userdata['name'] = $user->username;  //会员账号
		$userdata['money'] = $user->balance;  //会员余额
		$userdata['boxmoney'] = 0;  //会员保险箱余额
		$userdata['invite_code'] = $user->id;  //会员推广码
		$userdata['real_name'] = $user->realname;  //会员真实姓名
		$userdata['phone'] = $user->phone;  //会员手机
		$userdata['email'] = $user->mail;  //会员邮箱
		$userdata['qq'] = '';  //会员QQ
		$userdata['weixin'] = '';  //会员微信
		$userdata['user_bank'] = $UserCard;  //会员微信
		/*$userdata['bank_username'] = '姓名';  //会员银行卡姓名
		$userdata['bank_name'] = '遵义市商业银行';  //会员银行名称
		$userdata['bank_address'] = '开户地址';  //会员银行开户地址
		$userdata['bank_card'] = '123456789';  //会员银行卡号*/
		
		return $this->returnMsg(200,$userdata,'成功');		
	}
    public function getMoney(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}		
		$TransferLog = TransferLog::where('transfer_type', 0)->where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if($TransferLog && $TransferLog->api_type != ''){	
		    $tg = new TgService; 
			$result = $tg->balance($TransferLog->api_type,$user->username);
			if($result['code'] != 200){
				return $this->returnMsg(201,'',$result['message']);	
			}
			$api_money = $result['data'];
		}			
		$money = $user->balance + $api_money;
		return $this->returnMsg(200,$money,'成功');		
	}
    public function userChildren(Request $request)
    {
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}		
		$where = array();
		if($request->has('name')){
			$where['username'] = $data['name'];
		}
		$where['pid'] = $user->id;
	    $user_list = User::where($where)->orderBy('id','desc')->get()->toArray();
		$zhishu_xinzeng = User::where('pid',$user->id)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count();
		$ziying_yeji = GameRecord::where("user_id", $user->id)->where("status", 1)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->sum('win_loss');
		$wode_zonge = GameRecord::where("user_id", $user->id)->where("status", 1)->whereBetween('created_at', [date('Y-m-d H:i:s',time() - 60 * 1440 * 30), date('Y-m-d H:i:s',time())])->sum('bet_amount');
		$weilingfanshui = TransferLog::where("user_id", $user->id)->where("transfer_type", 6)->where("state", 0)->whereBetween('created_at', [date('Y-m-d H:i:s',time() - 60 * 1440 * 30), date('Y-m-d H:i:s',time())])->sum('money');
		$lingqufanshui = TransferLog::where("user_id", $user->id)->where("transfer_type", 6)->where("state", 1)->whereBetween('created_at', [date('Y-m-d H:i:s',time() - 60 * 1440 * 30), date('Y-m-d H:i:s',time())])->sum('money');
		$zhishu_liushui_total = 0;
        $zhishu_yeji = 0;
		foreach($user_list as $key => $value){
			$gmaefecord = GameRecord::where("user_id", $value['id'])->where("status", 1)->whereBetween('created_at', [date('Y-m-d H:i:s',time() - 60 * 1440 * 30), date('Y-m-d H:i:s',time())])->sum('bet_amount');
			$user_data = User::where('pid',$value['id'])->count();
			$user_list[$key]['total_amount'] = $gmaefecord;
			$user_list[$key]['zhishu'] = $user_data;
			$zhishu_liushui_total+=$gmaefecord;
			
			$zhishu_touzhu = GameRecord::where("user_id", $value['id'])->where("status", 1)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->sum('win_loss');
			$zhishu_yeji += $zhishu_touzhu;
		}
        return response()->json([
            'code'    => 200,
            'message' => '成功',
            'data'    => $user_list,
			'zhishu_total' => count($user_list),  //直属人数
			'zhishu_liushui_total' => $zhishu_liushui_total,  //直属总流水
			'zhishu_xinzeng' => $zhishu_xinzeng, //直属新增
			'zhishu_yeji' => $zhishu_yeji,  //今日直属业绩
			'ziying_yeji' => $ziying_yeji, //今日自营业绩
			'wode_zonge' => $wode_zonge, //我的投注总额
			'weilingfanshui' => $weilingfanshui, //未领取反水
			'lingqufanshui' => $lingqufanshui //已领反水
        ]);		
	}

	public function Regurgitation(Request $request){
        $data = $request->all();		
        $user = User::where('api_token',$data['lastsession'])->first();
        if (!$user) return $this->returnMsg(202,'','会员不存在或登陆信息已过期');		
        $TransferLog = TransferLog::where("user_id", $user->id)->where("transfer_type", 6)->where("state", 0)->whereBetween('created_at', [date('Y-m-d H:i:s',time() - 60 * 1440 * 30), date('Y-m-d H:i:s',time())])->get()->toArray();
        $money = 0;
		foreach($TransferLog as $key => $value){
			$TransferLogs = TransferLog::where("id", $value['id'])->first();
			$TransferLogs->state = 1;
			$TransferLogs->save();
			$money += $value['money'];
		}
		if($money > 0){
			$user->increment('balance', $money);
			return $this->returnMsg(200,'','操作成功');
		}
		return $this->returnMsg(201,'','没有可领取的反水');
	}
	public function GetTeamMember($members, $mid) {
		$Teams=array();//最终结果
		$mids=array($mid);//第一次执行时候的用户id
		do {
			$othermids=array();
			$state=false;
			foreach ($mids as $valueone) {
				foreach ($members as $key => $valuetwo) {
					if($valuetwo['pid']==$valueone){
						$Teams[]=$valuetwo['id'];//找到我的下级立即添加到最终结果中
						$othermids[]=$valuetwo['id'];//将我的下级id保存起来用来下轮循环他的下级
						array_splice($members,$key,1);//从所有会员中删除他
						$state=true;   
					}
				}          
			}
			$mids=$othermids;//foreach中找到的我的下级集合,用来下次循环
		} while ($state==true);
	 
		return count($Teams);
	}	
    public function service_center(Request $request)
    {
        $notice = Article::where('cateid',6)->limit(3)->select("name as title","content","created_at as time")->orderBy('stor','asc')->get()->toArray();
		foreach($notice as $k => $v){
			$notice[$k]['content'] = strip_tags($v['content']);
		}
		return $this->returnMsg(200,$notice,'成功');		
	}
    public function systeminfo(Request $request)
    {
		$data = array();
		if($request->has('lastsession')){
			$user = User::where('api_token',$request->lastsession)->first();
            $wapurl = env("WAP_URL");
			$wapurl = explode(',', $wapurl);	
			$register_url = $wapurl[0].'/#/register?pid='.$user->id;

			$qrcodes = public_path('/qrcodes/');
			// qrcodes 目录不存在，则创建文件夹
			File::isDirectory($qrcodes) or File::makeDirectory($qrcodes, 0777, true, true);
			
			$img_file = 'qrcodes/'.$user['username'].'.png';
			if(!file_exists($img_file)){
				$QrCode = QrCode::format('png')->size(300)->generate($register_url,public_path($img_file));
			}			
			$data['qrcode'] = env('APP_URL').'/'.$img_file;
			
			
			$public_path = public_path('/inviterQrcodes/');
			// $public_path 目录不存在，则创建文件夹
			File::isDirectory($public_path) or File::makeDirectory($public_path, 0777, true, true);
            $inviterQrcodes = 'inviterQrcodes/'.$user['username'].'.png';
			if(!file_exists($inviterQrcodes)){
				$bg = imagecreatefrompng(public_path('/src_761chess_resource_img_extension_shareqrbg.png'));// 提前准备好的海报图
				$qrcode = imagecreatefrompng(public_path($img_file));
				imagecopyresampled($bg, $qrcode, 105, 365, 0, 0, 70, 70, imagesx($qrcode), imagesy($qrcode));
				imagepng($bg, public_path('/inviterQrcodes/' . $user['username'].'.png'));
			}			

			$data['inviterQrcodes'] = env('APP_URL').'/'.$inviterQrcodes;
		}
		$data['service_link'] = SystemConfig::getValue('kf_url');
		$data['qq'] = '';
		$data['wx'] = '';
		return $this->returnMsg(200,$data,'成功');		
	}


    public function querys(Request $request)
    {
		$notice = Article::where('name','like','%常见问题%')->first();
		return $this->returnMsg(200,$notice->content,'成功');		
	}	
    public function login(Request $request)
    {

        $rules = [
    		'name' => 'required',
            'password' => 'required|min:6',
    	];
        $this->validate($request,$rules,$this->messages);
        $data = $request->all();		
        $user = User::where('username',$data['name'])->first();
        if (!$user) return $this->returnMsg(202,'','会员不存在');
        if (Hash::check($data['password'],$user->password)) {
            $api_token = Str::random(60);
            $postdata['lastip'] = $request->getClientIp();
            $postdata['logintime'] = time();
            $postdata['loginsum'] =  $user->loginsum++;
            $postdata['api_token'] = $api_token;

            if(User::where('username',$data['name'])->update($postdata)){
				$UserCard = UserCard::where('user_id',$user->id)->select("id","bank as bank_name","bank_no as bank_card","bank_address","bank_owner as bank_username")->orderBy('id','desc')->get()->toArray();
                $userdata = array();
				$userdata['last_session'] = $api_token;  //会员登陆token
				$userdata['id'] = $user->id;   //会员id
				$userdata['agent'] = $user->isagent;   //是否代理
				$userdata['name'] = $user->username;  //会员账号
				$userdata['money'] = $user->balance;  //会员余额
				$userdata['boxmoney'] = 0;  //会员保险箱余额
				$userdata['invite_code'] = '';  //会员推广码
				$userdata['real_name'] = $user->realname;  //会员真实姓名
				$userdata['phone'] = $user->phone;  //会员手机
				$userdata['email'] = $user->mail;  //会员邮箱
				$userdata['qq'] = '';  //会员QQ
				$userdata['weixin'] = '';  //会员微信
				$userdata['user_bank'] = $UserCard;  //会员银行卡信息
				/*$userdata['bank_username'] = '';  //会员银行卡姓名
				$userdata['bank_name'] = '';  //会员银行名称
				$userdata['bank_address'] = '';  //会员银行开户地址
				$userdata['bank_card'] = '';  //会员银行卡号*/
                return $this->returnMsg(200,$userdata,'成功');
            }         
            return $this->returnMsg(203,'','登陆失败,请联系客服');
        } else {
            return $this->returnMsg(203,'','密码错误');
        }		
    }	
    public function hall_list(Request $request)
    {
		//游戏类型：1真人,2捕鱼,3电子,4彩票,5体育,6棋牌,7电竞		
		$list = GameList::where('app_state',1)->select('name','platform_name as Code','category_id as GameType','game_code as GameCode','app_img')->orderBy('order_by','asc')->get()->toArray();
        foreach($list as $k => $v){
			if($v['GameType'] == 'realbet'){
				$list[$k]['GameType'] = 1;
			}
			if($v['GameType'] == 'sport'){
				$list[$k]['GameType'] = 5;
			}
			if($v['GameType'] == 'gaming'){
				$list[$k]['GameType'] = 7;
			}
			if($v['GameType'] == 'joker'){
				$list[$k]['GameType'] = 6;
			}
			if($v['GameType'] == 'lottery'){
				$list[$k]['GameType'] = 4;
			}
			if($v['GameType'] == 'concise'){
				$list[$k]['GameType'] = 3;
			}
			if($v['GameType'] == 'fishing'){
				$list[$k]['GameType'] = 2;
			}			
			$list[$k]['app_img'] = env('APP_URL').'/uploads/'.$v['app_img'];
		}
		$hot = GameListApp::where('app_state',1)->select('name','platform_name as Code','category_id as GameType','game_code as GameCode','app_img')->orderBy('order_by','asc')->get()->toArray();
        foreach($hot as $k => $v){
			if($v['GameType'] == 'realbet'){
				$hot[$k]['GameType'] = 1;
			}
			if($v['GameType'] == 'sport'){
				$hot[$k]['GameType'] = 5;
			}
			if($v['GameType'] == 'gaming'){
				$hot[$k]['GameType'] = 7;
			}			
			if($v['GameType'] == 'joker'){
				$hot[$k]['GameType'] = 6;
			}
			if($v['GameType'] == 'lottery'){
				$hot[$k]['GameType'] = 4;
			}
			if($v['GameType'] == 'concise'){
				$hot[$k]['GameType'] = 3;
			}
			if($v['GameType'] == 'fishing'){
				$hot[$k]['GameType'] = 2;
			}			
			$hot[$k]['app_img'] = env('APP_URL').'/uploads/'.$v['app_img'];
		}
        $data['list'] = $list;
        $data['hot'] = $hot;		
	    return $this->returnMsg(200,$data,'成功');
	}
    public function update_password(Request $request)
    {
        $rules = [
            'password' => 'required|min:6',
    	];
        $this->validate($request,$rules,$this->messages);		
		$data = $request->all();
        $old_password = $data['old_password'];
        $password = $data['password'];
        $lastsession = $data['lastsession'];
		$type = $data['type'];
        $user = User::where('api_token',$lastsession)->lockForUpdate()->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}		
		if (!Hash::check($old_password,$user->password)) return $this->returnMsg(205,[],'原密码错误');
        $user->password = Hash::make($password);
        $user->save();
        if($user->save()){
            return $this->returnMsg(200);
        }else{
            return $this->returnMsg(300,[],'修改失败');
        }		
	}	
    public function api_login(Request $request)
    {
		$data = $request->all();
        $api_code = $data['Code'];
        $gameCode = $data['GameCode'];
        $gameType = $data['GameType'];
		$lastsession = $data['lastsession'];
        $is_mobile_url = 1;

        // 获取API信息
        $apiInfo = Api::where('api_code', $api_code)->first();
        if (!$apiInfo || (int)$apiInfo->state !== 1) {
            return $this->returnMsg(500, '', '该游戏接口已关闭');
        }
        
        $gameItem = null;
        $apiService = null;
        $venueCode = '';
        
        // 通过游戏的api_id获取接口服务类
        if (!empty($gameCode)) {
            $gameItem = GameList::where('platform_name', $api_code)
                ->where('game_code', $gameCode)
                ->first();
            if ($gameItem && ((int)$gameItem->app_state !== 1)) {
                return $this->returnMsg(500, '', '该游戏已关闭');
            }
            
            // 如果游戏记录存在且有关联的api_id，获取api_service
            if ($gameItem && $gameItem->api_id) {
                $gameApiInfo = Api::find($gameItem->api_id);
                if ($gameApiInfo && $gameApiInfo->api_service) {
                    $apiService = $gameApiInfo->api_service;
                    $venueCode = $gameItem->venue_code ?? '';
                }
            }
        }
        
        // 如果没有从游戏记录获取到api_service，使用默认的apiInfo的api_service
        if (!$apiService && $apiInfo->api_service) {
            $apiService = $apiInfo->api_service;
        }
        
        // 动态加载服务类
        $service = null;
        if ($apiService) {
            $serviceClassName = 'App\\Services\\' . ucfirst($apiService) . 'Service';
            if (class_exists($serviceClassName)) {
                $service = new $serviceClassName();
            }
        }
        
        // 如果没有找到对应的服务类，使用默认的TgService
        if (!$service) {
            $service = new TgService();
        }
                
        $user = User::where('api_token',$lastsession)->lockForUpdate()->first();
		if(!$user){
			return $this->returnMsg(201,'','登陆信息已过期');
		}
		
        // 判断是否为DbService，如果是则跳过注册
        $isDbService = ($apiService && strtolower($apiService) === 'db');
        
        if (!$isDbService) {
            // 非DbService，继续使用原有的注册逻辑
            $tg = new TgService;
            $User_Api = User_Api::where('api_code',$api_code)->where('user_id',$user->id)->first();
            if(!$User_Api){
                $result = $tg->register($user->username, '123456', $api_code);
                if($result['code'] != 200){
                    return $this->returnMsg(201, '', $result['message']);
                }
                $arr = [
                    'user_id' => $user->id,
                    'api_user' => $user->username,
                    'api_pass' => 123456,
                    'api_code' => $api_code,
                ];
                $User_Api = User_Api::create($arr);		    
            }
        
            //if($user->transferstatus == 1){
                $mz = $this->allmz($api_code,$user->id);
                if($mz['code'] != 200){
                    return $this->returnMsg(500,[],$mz['message']);				
                }
            //}
            
            $res = $tg->login($user->username, '123456', $api_code, $gameType, $is_mobile_url, $gameCode);
        } else {
            // DbService，跳过注册，直接调用登录接口
            if (empty($venueCode)) {
                return $this->returnMsg(500, '', '游戏场馆编码不能为空');
            }
            
            // DbService的login方法参数：login($userName, $venueCode, $currency = 'USDT', $gameId = 0, $deviceType = 2, $lang = 'zh_CN', $userClientIp = '')
            $deviceType = $is_mobile_url ? 2 : 1; // 1=pc, 2=h5
            $gameId = $gameCode ? intval($gameCode) : 0;
            $userClientIp = $request->ip();
            
            $res = $service->login($user->username, $venueCode, 'USDT', $gameId, $deviceType, 'zh_CN', $userClientIp);
        }
        
        if ($res['code'] == 200) {
            return $this->returnMsg(200, ['url' => $res['data']]);
        } else {
            return $this->returnMsg(500,$res,$res['message']);
        }		
	}
    public function allmz($plat_name,$userid){
        $user = User::where('id',$userid)->first();
		$tg = new TgService;
		$TransferLog = TransferLog::where('transfer_type', 0)->where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if($TransferLog && $TransferLog->api_type != $plat_name){			
			$result = $tg->balance($TransferLog->api_type,$user->username);
			if($result['code'] != 200){
				return $result;
			}
			$api_money = $result['data'];
			if($api_money >= '1'){
				$api_money = intval($api_money);
				$client_transfer_id = time() . $user->id . rand(100000, 999999);
				$arr = [
					'order_no' => $client_transfer_id,
					'api_type' => $TransferLog->api_type,
					'user_id' => $user->id,
					'transfer_type' => 1,
					'money' => $api_money,
					'cash_fee' => 0,
					'real_money' => $api_money,
					'before_money' => $user->balance ,
					'after_money' => $user->balance + $api_money,
					'state' => 2
				];
				$Transfers_id = TransferLog::create($arr);

				$res = $tg->withdrawal($user->username, $api_money, $client_transfer_id, $TransferLog->api_type);
				if($res['code'] != 200){
					$Transfers_id->delete();
					return $res;
				}
				$user->increment('balance', $api_money);
				$transferlog = TransferLog::where('order_no', $client_transfer_id)->first();
				$transferlog->state = 1;
				$transferlog->save();
				$user_api = User_Api::where('api_code', $TransferLog->api_type)->where('user_id', $user->id)->where('api_user', $user->username)->first();
				if($user_api->api_money <= $api_money){
					$user_api->api_money = 0;
					$user_api->save();						
				}else{
					$user_api->api_money -= $api_money;
					$user_api->save();						
				}
			}
		}
        $balance = $user->balance;
		
		if($balance >= '1'){            
			$client_transfer_id = time() . $user->id . rand(100000, 999999);
			$arr = [
				'order_no' => $client_transfer_id,
				'api_type' => $plat_name,
				'user_id' => $user->id,
				'transfer_type' => 0,
				'money' => -$balance,
				'cash_fee' => 0,
				'real_money' => -$balance,
				'before_money' => $user->balance ,
				'after_money' => $user->balance - $balance,
				'state' => 2
			];
			$Transfers_id = TransferLog::create($arr);
            $balance = intval($balance);
			$res = $tg->deposit($user->username, $balance, $client_transfer_id, $plat_name);
			if($res['code'] != 200){
				$Transfers_id->delete();
				return $res;
			}
			$user->decrement('balance', $balance);
			$transferlog = TransferLog::where('order_no', $client_transfer_id)->first();
			$transferlog->state = 1;
			$transferlog->save();
			$user_api = User_Api::where('api_code', $plat_name)->where('user_id', $user->id)->where('api_user', $user->username)->first();
			$user_api->increment('api_money', $balance);
		}
        return array('code' => 200, 'message' => '成功');
	} 
    public function write_log($data,$filepath=''){
        $data = is_array($data) ? json_encode($data) : $data;
        $data = date('Y-m-d H:i:s') . '   ' . $data;

        $filepath = $filepath ? $filepath : './app_log.txt';
        if($rsp = fopen($filepath, "a+b")) {
            fwrite($rsp, $data);
            fwrite($rsp, PHP_EOL."--------------------".PHP_EOL);
            fclose($rsp);
        }
    }
	public function float_number($num){
		if ($num >= 100000000) {
			$num = round($num / 100000000, 2) . '亿+';
		} else if ($num >= 10000000) {
			$num = round($num / 10000000, 3) . '千万';
		} else if ($num >= 10000) {
			$num = round($num / 10000, 2) . '万';
		}
		return $num;
	}
    public function http_post_json($url, $jsonStr)
    {		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }	
}