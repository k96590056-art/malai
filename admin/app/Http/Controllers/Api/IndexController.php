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
use App\Models\GameRecord;
use App\Models\AgentApply;
use App\Models\GameList;
use App\Models\GameListApp;
use App\Models\Sponsor;
use App\Models\GameCategory;

class IndexController extends Controller
{
    protected $messages = [];
    protected $game_list ;
    protected $banklist;

    public function __construct()
    {
        $tg = New TgService;

        $this->game_list =$tg->getallgamename();
        $this->gamemoney_list =$tg->getallmoneygamelist();
        $this->banklist = ['工商银行'=>'Icbc','中国农业银行'=>'Abc','招商银行'=>'Cmb','建设银行'=>'Ccb','中信银行'=>'Cibk','中国银行'=>'Boc','交通银行'=>'Bocom','华夏银行'=>'Hxbc','民生银行'=>'Cmbc','光大银行'=>'Cebc','兴业银行'=>'Fjib','浦发银行'=>'Spdb'];
        
        // 改进域名验证逻辑，使用 Request 对象而不是 $_SERVER
        $domain = SystemConfig::getValue('safe_domain');
        if ($domain) {
            $allowedDomains = array_filter(array_map('trim', explode(',', $domain)));
            
            // 严格验证域名格式
            $allowedDomains = array_filter($allowedDomains, function($domain) {
                return filter_var($domain, FILTER_VALIDATE_URL) && 
                       in_array(parse_url($domain, PHP_URL_SCHEME), ['http', 'https']);
            });
            
            // 注意：这里不能直接使用 request() 函数，因为构造函数中还没有 Request 对象
            // 实际的域名验证应该在具体的请求方法中进行
        }
    }
    
    public function test(){
        echo 123;die;
    }
    
    public function credit(Request $request)
    {
        $api_code = $request->input('api_code');
		$tg = New TgService;
		$data = $tg->credit($api_code);
        return $data;
    }	
    /**
     * 公告列表
     *
     * @return void
     */
    public function bannerList(Request $request)
    {
        $type = $request->input('type') ?? 2;
        $bannerlist = array(
            ["src"=>"/static/style/2ddb21b7a3564870bbac1b02e05b3f8d.jpg","background"=>"#f4f6ff"],
            ["src"=>"/static/style/008dc0a27cdf42708dcce9b516695469.jpg","background"=>"rgb(100, 61, 202)"],
            );
        $notice = Banner::where('type',$type)->select("pic as src","jump_url")->get()->toArray();    
       
        if(count($notice)){
            $bannerlist=[];
            foreach ($notice as $val){
                $bannerlist[]=["src"=>env('APP_URL').'/uploads/'.$val['src'],"background"=>"#f4f6ff",'url'=>$val['jump_url']] ;
            }        
        }
        return $this->returnMsg(200, $bannerlist);
    }
    
    public function article(Request $request)
    {
        $type = $request->input('type');
        $data = Article::where('cateid',$type)->first();
        return $this->returnMsg(200,$data);
    }
    
    /**
     * 公告列表
     *
     * @return void
     */
    public function Systemstatus()
    {
         $isclose = SystemConfig::query()->find("isclose");
         $data =[];
        if($isclose['value']){
            $webcontent = SystemConfig::query()->find("webcontent");
            $data['content'] = $webcontent['value'];
            $data['isclose'] = 0;
        }else{
            $data['content'] = '';
            $data['isclose'] = 0;
        }
        return $this->returnMsg(200, $data);
    }
        

    /**
     * 通知公告列表
     *
     * @return void
     */
    public function uservip(Request $request)
    {
        $vip = UserVip::get();
        return $this->returnMsg(200, $vip);
    }    
    /**
     * 通知公告列表
     *
     * @return void
     */
    public function homenotice(Request $request)
    {
        $notice = Article::where('cateid',6)->limit(3)->select("name")->get();
        $shownotice=[];
        foreach ($notice as $val){
            $shownotice[]=$val['name'];
        }        
        return $this->returnMsg(200, $shownotice);
    }
    /**
     * 通知公告列表
     *
     * @return void
     */
    public function homecontent(Request $request)
    {
        $notice = Article::where('cateid','<>',6)->get();
        return $this->returnMsg(200, $notice);
    }      
    /**
     * 通知公告列表
     *
     * @return void
     */
    public function homenoticelist(Request $request)
    {
        $notice = Article::where('cateid',6)->paginate(10);
        return $this->returnMsg(200, $notice);
    }   
    /**
     * 通知公告列表
     *
     * @return void
     */
    public function homenoticedeatil(Request $request)
    {
        $data = $request->all();
        $notice = Article::where('id',$data['id'])->first();
        return $this->returnMsg(200, $notice);
    }    
    /**
     * 公告列表
     *
     * @return void
     */
    public function cateList(Request $request)
    {
        $list = array(
            ["id"=>1,"pid"=>0,"name"=>"电子游艺","enname"=>"concise"],
            ["id"=>2,"pid"=>0,"name"=>"棋牌游戏","enname"=>"joker"],
            ["id"=>3,"pid"=>0,"name"=>"视讯直播","enname"=>"realbet"],
            ["id"=>4,"pid"=>0,"name"=>"彩票游戏","enname"=>"lottery"],
            ["id"=>5,"pid"=>0,"name"=>"电竞游戏","enname"=>"gaming"],
            ["id"=>6,"pid"=>0,"name"=>"体育赛事","enname"=>"sport"],
            );
        return $this->returnMsg(200, $list);
    }    
    /**
     * 个人消息
     *
     * @return void
     */
    public function noticeList(Request $request)
    {
        $rules = [
            'limit' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
        $this->validate($request, $rules, $this->messages);
        $data = $request->all();
        $limit = $data['limit'] ?? 10;
        $list = Message::orderBy('id', 'desc')->paginate($limit);
        return $this->returnMsg(200, $list);
    }
    /**
     * 活动类型
     *
     * @return void
     */
    public function activityType()
    {
        $list = ActivityType::all();
        return $this->returnMsg(200, $list);
    }

    /**
     * 活动列表
     *
     * @param Request $request
     * @return void
     */
    public function activityList(Request $request)
    {
        $rules = [
            'type' => 'nullable|integer'
        ];
        $this->validate($request, $rules, $this->messages);
        $data = $request->all();
        $type = $data['type'] ?? '';
        $list = Activity::when($type, function ($query) use ($type) {
            return $query->where('type', $type);
        })->where('state',1)->select('id','title','type','entitle','apply_count','banner','can_apply','state','created_at')->orderBy('id', 'desc')->paginate(99);
		foreach($list as $key => $value){
			$list[$key]['banner'] = env('APP_URL').'/uploads/'.$value['banner'];
		}
        return $this->returnMsg(200, $list);
    }
    /**
     * 活动详情
     *
     * @param Request $request
     * @return void
     */    
    public function activitydeatil(Request $request)
    {
        $rules = [
            'id' => 'nullable|integer'
        ];
        $this->validate($request, $rules, $this->messages);
        $data = $request->all();
        $id = $data['id'] ?? 0;
        $activity = Activity::where('id', $id)->first();
		$activity->app_img = env('APP_URL').'/uploads/'.$activity->app_img;
		$activity->banner = env('APP_URL').'/uploads/'.$activity->banner;
        return $this->returnMsg(200, $activity);
    }

    /**
     * 获取客服系统配置
     *
     * @return void
     */
    public function getServicerUrl()
    {
        // 获取客服系统配置
        $kefuUrl = SystemConfig::getValue('kf_url') ?? '';
        $gongdanUrl = SystemConfig::getValue('gongdan_url') ?? '';
        
        // 获取服务类型配置
        $serviceType = SystemConfig::getValue('service_type') ?? 'kefu';
        
        // 根据服务类型确定启用状态
        $kefuEnabled = ($serviceType === 'kefu') && !empty($kefuUrl);
        $gongdanEnabled = ($serviceType === 'gongdan');
        
        // 构建返回数据
        $data = [
            'kefu_url' => $kefuUrl,
            'gongdan_url' => $gongdanUrl,
            'kefu_enabled' => $kefuEnabled,
            'gongdan_enabled' => $gongdanEnabled,
            'default_system' => $serviceType,
            'show_selector' => false, // 不再显示系统选择器
            'domain' => env('APP_URL'),
            // 保持向后兼容
            'url' => $serviceType === 'kefu' ? $kefuUrl : '',
        ];
        
        return $this->returnMsg(200, $data);
    }


    /**
     * 获取游戏分类
     *
     * @param Request $request
     * @return void
     */
    public function getGameList(Request $request)
    {
        
        
        $platform = $request->input('platform_name') ?? '';
        $category = $request->input('game_type') ?? '';
        $list = GameList::when($platform,function ($query) use ($platform){
            return $query->where('platform_name',$platform);
        })->when($category,function ($query) use ($category){
            return $query->where('category_id',$category);
        })->where('is_top',1)->where('app_state',1)->select('name','game_code as gamecode','category_id','game_code','app_state')->orderBy('order_by','desc')->get();
        //return $this->returnMsg(200,$list);
        $gamelist =[];
        foreach($list as $val){
           $gamelist[$val->gamecode] =  $val->app_state;
        }
        $rules = [
            'game_type' => 'nullable',
        ];
        $this->validate($request, $rules, $this->messages);
        $data = $request->all();
        $game_type = $data['game_type'] ?? '';
        $tg = new TgService;
        $res = $tg->gametypelist($game_type);
        $gamelist1=[];
        foreach ($res['data'] as $vals){
            if((isset($gamelist[$vals['gamecode']]) && $gamelist[$vals['gamecode']]) || in_array($vals['gamecode'],['ae','fgdz','pp','obgdy'])){
                $gamelist1[] = $vals;
            }
        }
        
        return $this->returnMsg(200,$gamelist1);
        
    }

    /**
     * 获取游戏地址
     *
     * @param Request $request
     * @return void
     */
    public function getGameUrl(Request $request)
    {
        $rules = [
            'plat_name' => 'required',
            'game_type' => 'required',
            'game_code' => 'nullable',
            'is_mobile_url' => 'nullable',
        ];

        $this->validate($request, $rules, $this->messages);
        $data = $request->all();
     
        $api_code = $data['plat_name'];
        $gameCode = $data['game_code'] ?? '';
        $gameType = $data['game_type'];
        $is_mobile_url = $data['is_mobile_url'] ?? 1;

        $gameItem = null;
        $apiService = null;
        $venueCode = '';
        
        // 先检查热门游戏表（GameListApp）中是否有匹配的游戏
        $gameItemApp = null;
        if (!empty($gameCode)) {
            $gameItemApp = GameListApp::where('platform_name', $api_code)
                ->where('game_code', $gameCode)
                ->where('app_state', 1)
                ->first();
        } else {
            // 如果没有gameCode，尝试通过platform_name获取第一个热门游戏
            $gameItemApp = GameListApp::where('platform_name', $api_code)
                ->where('app_state', 1)
                ->first();
        }
        
        // 如果热门游戏存在且有外链（game_url），直接返回外链，跳过接口调用
        if ($gameItemApp && !empty($gameItemApp->game_url)) {
            return $this->returnMsg(200, ['url' => $gameItemApp->game_url]);
        }
        
        // 先通过plat_name获取游戏信息（GameList表）
        if (!empty($gameCode)) {
            $gameItem = GameList::where('platform_name', $api_code)
                ->where('game_code', $gameCode)
                ->first();
            if ($gameItem && (((int)$gameItem->site_state !== 1) || ((int)$gameItem->app_state !== 1))) {
                return $this->returnMsg(500, '', '该游戏已关闭');
            }
        } else {
            // 如果没有gameCode，尝试通过platform_name获取第一个游戏
            $gameItem = GameList::where('platform_name', $api_code)
                ->where('site_state', 1)
                ->where('app_state', 1)
                ->first();
        }
        
        // 如果游戏存在且有外链（game_url），直接返回外链，跳过接口调用
        if ($gameItem && !empty($gameItem->game_url)) {
            return $this->returnMsg(200, ['url' => $gameItem->game_url]);
        }
        
        // 通过游戏的api_id在apis表中获取api_service
        if ($gameItem && $gameItem->api_id) {
            $gameApiInfo = Api::find($gameItem->api_id);
            if ($gameApiInfo) {
                // 校验接口状态
                if ((int)$gameApiInfo->state !== 1) {
                    return $this->returnMsg(500, '', '该游戏接口已关闭');
                }
                // 获取api_service
                if ($gameApiInfo->api_service) {
                    $apiService = $gameApiInfo->api_service;
                }
                // 获取venue_code（如果是Db服务需要用到）
                $venueCode = $gameItem->venue_code ?? '';
            }
        }
        
        // 如果没有从游戏记录获取到api_service，使用默认的接口信息
        if (!$apiService) {
            $apiInfo = Api::where('api_code', $api_code)->first();
            if (!$apiInfo || (int)$apiInfo->state !== 1) {
                return $this->returnMsg(500, '', '该游戏接口已关闭');
            }
            if ($apiInfo->api_service) {
                $apiService = $apiInfo->api_service;
            }
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
        
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        
        $user = User::where('api_token',$token)->lockForUpdate()->first();

        // 定义游戏账号密码，默认值为123456
        $gamePassword = '123456';
        // 判断是否为DbService，如果是则跳过注册
        $isDbService = ($apiService && strtolower($apiService) === 'db');
        if (!$isDbService) {
            // 非DbService，继续使用原有的注册逻辑
            $User_Api = User_Api::where('api_code',$api_code)->where('user_id',$user->id)->first();
            if(!$User_Api){
                $result = $service->register($user->username, $gamePassword, $api_code);
                if($result['code'] != 200){
                    return $this->returnMsg(500, isset($result["data"]) ? $result["data"] : [], $result['message']);
                }
                $arr = [
                    'user_id' => $user->id,
                    'api_user' => $user->username,
                    'api_pass' => $gamePassword,
                    'api_code' => $api_code,
                ];
                $User_Api = User_Api::create($arr);		    
            }
            
            if($user->transferstatus == 1){
                $mz = $this->allmz($api_code,$user->id);
                if($mz['code'] != 200){
                    return $this->returnMsg(500,[],$mz['message']);				
                }
            }
            
            $leixing = '1';
            if($gameType == 'sport'){
                $leixing = '5';
            }
            if($gameType == 'concise'){
                $leixing = '3';
            }
            if($gameType == 'gaming'){
                $leixing = '7';
            }
            if($gameType == 'joker'){
                $leixing = '6';
            }
            if($gameType == 'lottery'){
                $leixing = '4';
            }
            if($gameType == 'fishing'){
                $leixing = '2';
            }
            
            // DbzhenrenService的login方法参数：login($loginName, $loginPassword, $api_code, $deviceType, $oddType, $lang, $backurl, $domain, $showExit, $gameTypeId, ...)
            $deviceType = $is_mobile_url ? 2 : 1; // 1=PC, 2=H5
            if ($apiService && strtolower($apiService) === 'dbzhenren') {
                $res = $service->login($user->username, $gamePassword, $api_code, $deviceType);
            } else {
                $res = $service->login($user->username, $gamePassword, $api_code, $deviceType, 0, 1, '', '', 0, $leixing, 0, '', 0, '', 'v2');
            }
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
    
    /**
     * 进入游戏后自动转账到游戏账户
     * @return void
     */
    public function transToTgAccount($user,$plat_name, $game_type)
    {
        $tg = new TgService;
                $plat_name = ($plat_name=='fgdz') ? 'fg' : $plat_name;
                if ($user->balance > 0) {
                    $client_transfer_id = time() . $user->id . rand(1000, 9999);
                   $amount = $user->balance;
                    $res = $tg->trans($user->username, $user->balance, $client_transfer_id, $plat_name, $game_type);
                    if ($res['code'] == 200) {
                        $user->balance = 0;
                        $user->save();
                        $arr = [
                            'order_no' => $client_transfer_id,
                            'api_type' => $plat_name,
                            'user_id' => $user->id,
                            'transfer_type' => 0,
                            'money' => -$amount,
                            'cash_fee' => 0,
                            'real_money' => $amount,
                            'before_money' =>$amount ,
                            'after_money' =>0,
                            'state' => 1
                        ];
                        TransferLog::create($arr);
                         Usersmoney::addinfo($user->id, $plat_name, $amount);
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }


    }    
    /**
     * 下注记录
     *
     * @param Request $request
     * @return void
     */
    public function betRecord(Request $request)
    {

        $data = $request->all();
        $start = $end = '';
        if (isset($data['date'])) {
            switch($data['date']){
                case 1:
                    list($start, $end) = [date('Y-m-d 00:00:00',time()), date('Y-m-d 23:59:59',time())];
                    break;
                case 2:
                    list($start, $end) =  [date('Y-m-d 00:00:00',time()-7*60*60*24), date('Y-m-d 23:59:59',time())];
                    break;  
                case 3:
                    list($start, $end) =[date('Y-m-d 00:00:00',time()-15*60*60*24), date('Y-m-d 23:59:59',time())];
                    break; 
                case 4:
                    list($start, $end) =[date('Y-m-d 00:00:00',time()-30*60*60*24), date('Y-m-d 23:59:59',time())];
                    break;                     
            }
        }
        $api_type = $data['api_type'] ?? '';
    
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first();
        $pagesize = isset($data['pagesize']) ? $data['pagesize'] : 10 ;
        
                $list = GameRecord::where('user_id', $user->id)
                
                  ->when($api_type, function ($query) use ($api_type) {
                        return $query->where('platform_type', strtolower($api_type));
                    })
                    ->when($start, function ($query) use ($start) {
                        return $query->where('created_at', '>=', $start);
                    })->when($end, function ($query) use ($end) {
                        return $query->where('created_at', '<=', $end);
                    })->orderBy('id', 'desc')->select('bet_id','bet_time','platform_type','bet_amount','win_loss','status')->paginate($pagesize);
                    foreach ($list as $k => $v) {
                        $list[$k]['Code'] =$this->game_list[$v['platform_type']] ?? '';
                      
                    }                        
           
        return $this->returnMsg(200, $list);
    }

    /**
     * 获取游戏
     *
     * @return void
     */
    public function getdogame()
    {
        $gamelist = $this->game_list;
        //$game =[];
       // foreach ($gamelist as $key=>$val){
       //     $game[]=['id'=>$key,'name'=>$val];
       // }
        unset($gamelist['universal']);
        return $this->returnMsg(200, $gamelist);
    }


    /**
     * 交易记录
     *
     * @return void
     */
    public function transRecord(Request $request)
    {
        $data = $request->all();
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first();        
        $start = $end = '';
        if (isset($data['date'])) {
            switch($data['date']){
                case 1:
                    list($start, $end) = [date('Y-m-d 00:00:00',time()), date('Y-m-d 23:59:59',time())];
                    break;
                case 2:
                    list($start, $end) =  [date('Y-m-d 00:00:00',time()-7*60*60*24), date('Y-m-d 23:59:59',time())];
                    break;  
                case 3:
                    list($start, $end) =[date('Y-m-d 00:00:00',time()-15*60*60*24), date('Y-m-d 23:59:59',time())];
                    break; 
                case 4:
                    list($start, $end) =[date('Y-m-d 00:00:00',time()-30*60*60*24), date('Y-m-d 23:59:59',time())];
                    break;                     
            }
        }
        $type = $data['type'];
        $api_type = $data['api_type'] ?? '';
        $pagesize = isset($data['pagesize']) ? $data['pagesize'] : 10 ;    
        $gamelist = $this->gamemoney_list;

        $pay_way =[1=>'银行卡',2 => '',3=>'支付宝',4=>'微信',5 => 'USDT-TRC20',6 => 'USDT-ERC20', 10 => '充值赠送', 11 => '代理充值', 66 => '客服代充'];
        switch ($type) {
            case 1:
                $list = Recharge::where('user_id', $user->id)
                    ->when($start, function ($query) use ($start) {
                        return $query->where('created_at', '>=', $start);
                    })->when($end, function ($query) use ($end) {
                        return $query->where('created_at', '<=', $end);
                    })->orderBy('id', 'desc')->select('amount','created_at','state','pay_way','out_trade_no')->paginate($pagesize);
                    foreach ($list as $k => $v) {
                        $list[$k]['pay_way'] = $pay_way[$v['pay_way']];
                        $list[$k]['amount'] = abs($v['amount']);
                    }                        
                break;
            case 2:
                $pay_way = [0 => '未记录',1 => '银行卡',2 => 'USDT-TRC20',3 => 'USDT-ERC20', 67 => '客服代扣'];
                $list = Withdraw::where('user_id', $user->id)
                    ->when($start, function ($query) use ($start) {
                        return $query->where('created_at', '>=', $start);
                    })->when($end, function ($query) use ($end) {
                        return $query->where('created_at', '<=', $end);
                    })->orderBy('id', 'desc')->select('real_money','created_at','state','order_no as out_trade_no','type')->paginate($pagesize);
                    foreach ($list as $k => $v) {
                        $list[$k]['pay_way'] = $pay_way[$v['type']];
                        $list[$k]['amount'] = abs($v['real_money']);
                    }                       
                break; 
            case 3:
                $list = TransferLog::where('user_id', $user->id)->where('transfer_type', 0)
                    ->when($start, function ($query) use ($start) {
                        return $query->where('created_at', '>=', $start);
                    })->when($end, function ($query) use ($end) {
                        return $query->where('created_at', '<=', $end);
                    })->when($api_type,function ($query) use ($api_type){
                        return $query->where('api_type',$api_type);
                    })->select('real_money','created_at','state','api_type')->orderBy('id', 'desc')->paginate($pagesize);
                  
                    foreach ($list as $k => $v) {
                        $list[$k]['pay_way'] = $gamelist[$v['api_type']];
                        $list[$k]['amount'] = abs($v['real_money']);
                    }                    
                break; 
            case 4:
                $list = TransferLog::where('user_id', $user->id)->whereIn('transfer_type', [1,3])
                    ->when($start, function ($query) use ($start) {
                        return $query->where('created_at', '>=', $start);
                    })->when($end, function ($query) use ($end) {
                        return $query->where('created_at', '<=', $end);
                    })->when($api_type,function ($query) use ($api_type){
                        return $query->where('api_type',$api_type);
                    })->select('real_money','created_at','state','api_type')->orderBy('id', 'desc')->paginate($pagesize);
                    foreach ($list as $k => $v) {
                        if($v['api_type']=='web'){
                            $list[$k]['pay_way'] ='优惠活动';
                        }else{
                            $list[$k]['pay_way'] = $gamelist[$v['api_type']];
                        }
                        
                        $list[$k]['amount'] = abs($v['real_money']);
                    }
                break;                 
            default:
                // code...
                break;
        }

        return $this->returnMsg(200, $list);

    }


    /**
     * 交易记录
     *
     * @return void
     */
    public function rechargeRecord(Request $request)
    {

        $data = $request->all();
        $start = $end = '';
        if (isset($data['time'])) {
            list($start, $end) = [$data['time'][0], $data['time'][1]];
        }

        $list = Recharge::where('user_id', Auth::id())
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->orderBy('id', 'desc')->paginate(10);
        foreach ($list as $k => $v) {
            //$list[$k]['state'] = $this->state[$v->state];

            $list[$k]['type'] = ($v->pay_way == 10) ? '充值赠送' : '充值';
        }
        return $this->returnMsg(200, $list);

    }


    /**
     * 交易记录
     *
     * @return void
     */
    public function WithdrawRecord(Request $request)
    {
        $data = $request->all();
        $start = $end = '';
        if (isset($data['time'])) {
            list($start, $end) = [$data['time'][0], $data['time'][1]];
        }


        $list = Withdraw::where('user_id', Auth::id())
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->orderBy('id', 'desc')->paginate(10);
        foreach ($list as $k => $v) {
            $list[$k]['state'] = $this->state[$v->state];
            $list[$k]['out_trade_no'] = $v->order_sn;
            $list[$k]['type'] = '提现';

        }
        return $this->returnMsg(200, $list);

    }

    public function userbalancelist(Request $request){
        $data = $request->all();
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first();          
        $Api = Api::where('state',1)->orderBy('order_by', 'asc')->get()->toArray();
		$data = array();
        foreach($Api as $key => $v){
			$User_Api = User_Api::where('api_code',$v['api_code'])->where('user_id',$user->id)->first();
            $data[$key]['balance'] = $User_Api ? sprintf("%.2f",$User_Api->api_money) : 0;
			$data[$key]['name'] = $v['api_name'];
			$data[$key]['platname'] = $v['api_code'];
			$data[$key]['app_icon'] = env('APP_URL').'/uploads/'.$v['app_icon'];
		}
        return $this->returnMsg(200, $data);
    }
    public function userapimoney(Request $request)   
    {
        $api_code = $request->route('api_code');
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first();
		$User_Api = User_Api::where('api_code',$api_code)->where('user_id',$user->id)->first();
		$tg = New TgService;
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
        $result = $tg->balance($api_code,$user->username);
		if($result['code'] != 200){
			return $this->returnMsg(201, '', $result['message']);
		}		
		$User_Api->api_money = $result['data'];
		$User_Api->save();		
        return $this->returnMsg(200,['balance' => $result['data']]);      
    } 
    public function uptransferstatus(Request $request){
            $data = $request->all();
            $token = $request->header('authorization');
            $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first();  
            
            // 过滤掉中间件注入的 current_user 字段，避免 SQL 报错
            if (isset($data['current_user'])) unset($data['current_user']);
            
            $user->update($data);        
            return $this->returnMsg(200, '', '申请成功');
    }

    public function fanshui(Request $request){
        $data = $request->all();
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first();         
        $start = $end = '';
        $pagesize = isset($data['pagesize']) ? $data['pagesize'] : 10 ;
        if (isset($data['date'])) {
            switch($data['date']){
                case 1:
                    list($start, $end) = [date('Y-m-d 00:00:00',time()), date('Y-m-d 23:59:59',time())];
                    break;
                case 2:
                    list($start, $end) =  [date('Y-m-d 00:00:00',time()-7*60*60*24), date('Y-m-d 23:59:59',time())];
                    break;  
                case 3:
                    list($start, $end) =[date('Y-m-d 00:00:00',time()-15*60*60*24), date('Y-m-d 23:59:59',time())];
                    break; 
                case 4:
                    list($start, $end) =[date('Y-m-d 00:00:00',time()-30*60*60*24), date('Y-m-d 23:59:59',time())];
                    break;                     
            }
        }
        $api_type = $data['api_type'];
        $type =  $data['type'];


        $lists = TransferLog::where('user_id', $user->id)->where('transfer_type', 6)
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })->when($api_type, function ($query) use ($api_type) {
                return $query->where('platform_type', '=', $api_type);
            })->when($type, function ($query) use ($type) {
                return $query->where('state', '=', ($type-1));
            })->orderBy('id', 'desc')->paginate($pagesize);

        foreach ($lists as $k => $v) {
            $lists[$k]['gamename'] = $this->game_list[$v['platform_type']];
        }
         $list['list'] = $lists;
         $list['jisuan'] = TransferLog::where('user_id', $user->id)->where('transfer_type', 6)->where('state', 1)->sum('real_money');
         $list['nojisuan'] = TransferLog::where('user_id',  $user->id)->where('transfer_type', 6)->where('state', 0)->sum('real_money');
        return $this->returnMsg(200, $list);
    }

    public function dofanshui(Request $request)
    {
        $data = $request->all();
         $token = $request->header('authorization');
         $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first();
                $betlist = TransferLog::where('user_id', $user->id)->where('state', 0)->where('transfer_type', 6)->select('betid')->get();
                $userfanshui = TransferLog::where('user_id', $user->id)->where('state', 0)->where('transfer_type', 6)->sum('real_money');
                if ($userfanshui) {
                    $userinfo = Users::where('id', $user->id)->lockForUpdate()->first();
                    $userinfo->balance = $userinfo->balance + $userfanshui;
                    $userinfo->save();
                    TransferLog::where('user_id', $user->id)
                        ->where('state', 0)
                        ->update(['state' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                    $betidarray=[];
                    foreach ($betlist as $val){
                        $betidarray[]=$val['betid'];
                    }
                    
                    GameRecord::where('user_id', $user->id)->whereIn('bet_id', $betidarray)->update(['is_back' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                    
                    return $this->returnMsg(200, '', '成功领取');
                } else {
                    return $this->returnMsg(202, '', '没有可领取的返水');
                }

    }

    public function banklist()
    {
        $banklist = Bank::where('state', 1)->get();
        foreach ($banklist as &$val){
            $val->ico= env('APP_URL').'/uploads/'. $val->bank_img;    
        }         
         return $this->returnMsg(200, $banklist);
    }
    
    public function getpaybank()
    {
		$cardlist = PaySetting::where('state',1)->get();
		foreach ($cardlist as &$val){
			if($val->bank_data->bank_name!='USDT' || $val->bank_data->bank_name!='银行类型后台添加'){
				$val->ico= env('APP_URL').'/uploads/'. $val->bank_data->bank_img; 
			}else{
				$val->ico='';
			}
		}        
         return $this->returnMsg(200, $cardlist);
    }    

    public function doactivity(Request $request){
            $data = $request->all();
             $token = $request->header('authorization');
             $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 

            $activity = Activity::where('id', $data['activityid'])->first();
            if(empty($activity)){
                return $this->returnMsg(202, '', '活动不存在');
            }

            $isapple = ActivityApply::where("user_id",$user->id)->where('activity_id',$data['activityid'])->first();
            if($isapple){
                if($isapple->state==1){
                    return $this->returnMsg(202, '', '您已经申请过，等待管理员审核');
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
                return $this->returnMsg(200, '', '申请失败');
            }

    }
    
    public function activityApplyLog(Request $request)
    {
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $limit = $request->input('limit') ?? 10;
        $list = ActivityApply::where("user_id",$user->id)->paginate($limit);
        foreach ($list as $k => $v) {
            $list[$k]->activity_name = Activity::find($v->activity_id)->title;
        }
        return $this->returnMsg(200,$list);
    }
    /**
     * 用户所有银行卡
     */
    public function getAllUserCard(Request $request)
    {
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 
        $list = UserCard::where('user_id', $user->id)->get();
        foreach ($list as &$val){
			if($val->bank!='USDT' && $val->bank != 'ebpay'){
				$banklist = Bank::where('bank_name', $val->bank)->first();
				$val->ico= env('APP_URL').'/uploads/'. $banklist->bank_img;    
			}else{
				$val->ico='';
			}
        }
        return $this->returnMsg(200, $list);
    }

    /**
     * 系统银行卡信息
     */
    public function systemBankCardInfo(Request $request)
    {
        $data = $request->all();
        if($data['payType']!=1){
            $card = PaySetting::where('state', 1)->where('bank_id','>', 1)->first();
        }else{
            $card = PaySetting::where('state', 1)->where('bank_id', 1)->first();
        }

        return $this->returnMsg(200, $card);
    }
    

    public function gameslist(Request $request)
    {
        $data = $request->all();
        $tg = new TgService;
        $gamelist = $tg->gameslist($data['gamecode']);
        $gamelist = $gamelist['data'];
       return $this->returnMsg(200, $gamelist);
    }
    
    public function  messagecenter(Request $request){
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        $map['user_id']=0;
        $map['vip_id']=0;
        $map['isagent']=0;
        
        $map1['isagent']=$user->agent;
        $map2['vip_id']=$user->vip;
        $map3['user_id']=$user->id;
        
        $data = $request->all();
        
        $list = Message::where('type',$data['type'])->whereOr($map)->whereOr($map1)->whereOr($map2)->whereOr($map3)->paginate(10);
        foreach ($list as $k => &$v) {
            $user_message = UserMessage::where('message_id', $v->id)->count();
            $v->is_read = $user_message ?? 0;
            $v->desc = mb_substr(strip_tags($v->content),0,20,'utf-8');
        }        
       
       return $this->returnMsg(200, $list);
    }  
    
    public function  message(Request $request){
     $token = $request->header('authorization');
     $token = str_replace('Bearer ','',$token) ;
            $user = User::where('api_token',$token)->first(); 
        $map['user_id']=0;
        $map['vip_id']=0;
        $map['isagent']=0;
        $map1['isagent']=$user->agent;
        $map2['vip_id']=$user->vip;
        $map3['user_id']=$user->id;
        
        $data = $request->all();
        
        $list = Message::where('id',$data['id'])->whereOr($map)->whereOr($map1)->whereOr($map2)->whereOr($map3)->first();
               
       
       return $this->returnMsg(200, $list);
    }   
    
    public function app()
    {
        // iOS相关配置
        $ios_download_url = SystemConfig::getValue('ios_download_url');
        $ios_download_qrcode = SystemConfig::getValue('ios_download_qrcode');
        $ios_download_qrcode = env('APP_URL').'/uploads/'.$ios_download_qrcode;
        
        // 新增Android相关配置
        $android_download_url = SystemConfig::getValue('android_download_url');
        $android_download_qrcode = SystemConfig::getValue('android_download_qrcode');
        $android_download_qrcode = $android_download_qrcode ? env('APP_URL').'/uploads/'.$android_download_qrcode : '';
        
        // 其他通用配置
        $h5_url = env('WAP_URL');
        $title = SystemConfig::getValue('site_title') ?? '娱乐城';
        $site_name = SystemConfig::getValue('site_name') ?? $title;
        $redpacket_switch = SystemConfig::getValue('redpacket');
        $app_download_switch = SystemConfig::getValue('app_download_switch', '1'); // 新增APP下载提示框开关
        $site_state = SystemConfig::getValue('site_state');
        $fanshui = SystemConfig::getValue('fanshui');
        $index_modal = SystemConfig::getValue('isclose');
        $repair_tips = SystemConfig::getValue('repair_tips');
        $webcontent = SystemConfig::getValue('webcontent');
        $site_logo = SystemConfig::getValue('site_logo');
        $site_logo = env('APP_URL').'/uploads/'.$site_logo;
        $app_logo = SystemConfig::getValue('app_logo');
        $app_logo = $app_logo ? env('APP_URL').'/uploads/'.$app_logo : '';
        
        // 包含Android配置项返回
        return $this->returnMsg(200,compact('ios_download_qrcode','ios_download_url','android_download_qrcode','android_download_url','h5_url','title','site_name','redpacket_switch','app_download_switch','site_state','fanshui','index_modal','repair_tips','webcontent','site_logo','app_logo'));
    }
    
    
    public function applyagentdo(Request $request)
    {
        $data = $request->all();
        $token = $request->header('authorization');
        $token = str_replace('Bearer ','',$token) ;
        $user = User::where('api_token',$token)->first(); 
        
        $useragent = AgentApply::where('user_id',$user->id)->first();
         if ($useragent)return $this->returnMsg(500, '', '您已申请过代理'); 

            $arr = [
                'user_id' => $user->id,
                'apply_info' => $data['apply_info'],
                'state' => 1,
                'mobile' => $data['mobile'],
            ];
        if($res = AgentApply::create($arr)){
          return $this->returnMsg(200, '', '申请成功');
        }else{
            return $this->returnMsg(500, '', '申请失败');
        }
    } 
    
    public function getAgentLoginUrl()
    {
        return $this->returnMsg(200,['url' => env('AGENT_LOGIN')]);
    }
    
    public function getVisitUrl(Request $request) {
        $origin = $_SERVER['HTTP_ORIGIN'];
        if($this->isMobile()){
            $wapurl = env("WAP_URL");
			$wapurl = explode(',', $wapurl);
			if(in_array($origin,$wapurl)){
				return $this->returnMsg(500,[],'wap');
			}else{
				return $this->returnMsg(200, ['url' => $wapurl[0]]);
			}
           
        } else {
            $url = env("PC_URL");
			$weburl = explode(',', $url);
			if(in_array($origin,$weburl)){
				return $this->returnMsg(500,[],'pc');
			}else{
				return $this->returnMsg(200, ['url' => $weburl[0]]);
			}
        }
    }
    
    public function getAllPlat()
    {
        $vaild_plat = GameList::where('app_state',1)->where('is_top',1)->select('platform_name')->distinct()->pluck('platform_name')->toArray();
        $res = array_unique($vaild_plat);
        return $this->returnMsg(200,$res);
    }
    
    /**
     * 获取游戏分类列表
     */
    public function getGameCategories(Request $request)
    {
        $categories = GameCategory::where('status', 1)
            ->orderBy('id', 'asc')
            ->select('id', 'name', 'code', 'icon')
            ->get();
        
        $list = [];
        foreach ($categories as $category) {
            $item = [
                'id' => $category->id,
                'name' => $category->name,
                'code' => $category->code,
                'icon' => $category->icon ? env('APP_URL') . '/uploads/' . $category->icon : null
            ];
            $list[] = $item;
        }
        
        return $this->returnMsg(200, $list);
    }
    
    public function getAllGameList(Request $request)
    {
        $platform = $request->input('platform') ?? '';
        $category = $request->input('category') ?? '';
        $list = GameList::when($platform,function ($query) use ($platform){
            return $query->where('platform_name',$platform);
        })->when($category,function ($query) use ($category){
            return $query->where('category_id',$category);
        })->where('is_top',1)->where('site_state',1)
        // 返回字段补充 is_hot 与 app_img，供前端"热门分类"与图片优先级展示使用
        ->select('name','platform_name','category_id','game_code','app_state','is_hot','check_yes_img','check_no_img','api_logo_img','mobile_img','header_logo','app_img','app_icon')
        ->orderBy('order_by','asc')->get()->toArray();
		// 预取 apis 表的 app_icon，优先使用接口管理里的图标
		$apiIcons = \DB::table('apis')->whereNotNull('app_icon')->pluck('app_icon','api_code')->toArray();
		foreach($list as $key => $value){
			// 不再检查 apis 表，无论是否存在对应的 platform_name 都展示游戏
			$list[$key]['check_yes_img'] = env('APP_URL').'/uploads/'.$value['check_yes_img'];
			$list[$key]['check_no_img'] = env('APP_URL').'/uploads/'.$value['check_no_img'];
			$list[$key]['api_logo_img'] = env('APP_URL').'/uploads/'.$value['api_logo_img'];
			$list[$key]['mobile_img'] = env('APP_URL').'/uploads/'.$value['mobile_img'];
			$list[$key]['header_logo'] = env('APP_URL').'/uploads/'.$value['header_logo'];
            if (!empty($value['app_img'])) {
                $list[$key]['app_img'] = env('APP_URL').'/uploads/'.$value['app_img'];
            }
            // 优先用 apis.app_icon，其次落回 game_lists.app_icon
            $apiCode = $value['platform_name'] ?? '';
            $iconPath = $apiIcons[$apiCode] ?? ($value['app_icon'] ?? '');
            $list[$key]['app_icon'] = $iconPath ? env('APP_URL').'/uploads/'.$iconPath : '';
		}
        $list = array_merge($list);
        
        // 获取 game_lists_app 表中的游戏（仅在热门分类显示）
        // 不合并到主列表，而是单独返回，由前端在热门分类中处理
        $appList = GameListApp::when($platform,function ($query) use ($platform){
            return $query->where('platform_name',$platform);
        })->where('app_state',1)
        ->select('name','platform_name','category_id','game_code','app_state','is_hot','app_img','app_icon')
        ->orderBy('order_by','asc')->get()->toArray();
        
        $appListFormatted = [];
        foreach($appList as $key => $value){
            // 不再检查 apis 表，无论是否存在对应的 platform_name 都展示游戏
            // 为 game_lists_app 的数据补充字段，使其格式与 game_lists 一致
            // 将 game_lists_app 中的所有游戏标记为热门，以便在热门列表中显示
            $appItem = [
                'name' => $value['name'],
                'platform_name' => $value['platform_name'],
                'category_id' => $value['category_id'],
                'game_code' => $value['game_code'],
                'app_state' => $value['app_state'],
                'is_hot' => 1, // game_lists_app 中的游戏都标记为热门
                'check_yes_img' => '',
                'check_no_img' => '',
                'api_logo_img' => '',
                'mobile_img' => !empty($value['app_img']) ? env('APP_URL').'/uploads/'.$value['app_img'] : '',
                'header_logo' => '',
                'app_img' => !empty($value['app_img']) ? env('APP_URL').'/uploads/'.$value['app_img'] : '',
            ];
            // 优先用 apis.app_icon，其次落回 game_lists_app.app_icon
            $apiCode = $value['platform_name'] ?? '';
            $iconPath = $apiIcons[$apiCode] ?? ($value['app_icon'] ?? '');
            $appItem['app_icon'] = $iconPath ? env('APP_URL').'/uploads/'.$iconPath : '';
            
            $appListFormatted[] = $appItem;
        }
        
        // 返回主列表和 app 列表，前端可以根据需要合并
        return $this->returnMsg(200, [
            'list' => $list,
            'app_list' => $appListFormatted // game_lists_app 中的游戏，仅在热门分类显示
        ]);
    }
    public function gamelistBycode(Request $request)
    {
        $list = GameList::where('site_state',1)->where('category_id','fishing')->orderBy('order_by','asc')->get()->toArray();
		$listarray = array();
		foreach($list as $key => $value){
			// 不再检查 apis 表，无论是否存在对应的 platform_name 都展示游戏
			$listarray[$key]['gamepic'] = env('APP_URL').'/uploads/'.$value['api_logo_img'];
			$listarray[$key]['catecode'] = $value['platform_name'];
			$listarray[$key]['gamename'] = $value['name'];
			$listarray[$key]['gamecode'] = $value['game_code'];
			$listarray[$key]['gametype'] = 'fishing';
		}
        $listarray = array_merge($listarray);
        return $this->returnMsg(200,$listarray);
    }   
    public function getAppUrl()
    {
        $url = env('APP_URL');
        return $this->returnMsg(200,compact('url'));
    }
    
    /**
     * 获取赞助商列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSponsorList()
    {
        $sponsors = Sponsor::active()
            ->published()
            ->ordered()
            ->select('id', 'name', 'title', 'logo', 'banner', 'link_url', 'link_type', 'content_type', 'content', 'description')
            ->get()
            ->map(function ($sponsor) {
                return [
                    'id' => $sponsor->id,
                    'name' => $sponsor->name,
                    'title' => $sponsor->title,
                    'logo' => $sponsor->logo_url,
                    'banner' => $sponsor->banner_url,
                    'link_url' => $sponsor->link_url,
                    'link_type' => $sponsor->link_type,
                    'content_type' => $sponsor->content_type,
                    'content' => $this->cleanContent($sponsor->content),
                    'description' => $sponsor->description,
                ];
            });
        
        return $this->returnMsg(200, $sponsors);
    }
    
    /**
     * 清理文章内容，确保编码正确
     *
     * @param string|null $content
     * @return string
     */
    private function cleanContent($content)
    {
        if (empty($content)) {
            return '';
        }
        
        // 确保内容是UTF-8编码
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }
        
        // 清理HTML实体
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // 移除可能的BOM标记
        $content = str_replace("\xEF\xBB\xBF", '', $content);
        
        return $content;
    }
}