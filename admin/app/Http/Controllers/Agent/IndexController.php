<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\GameRecord;
use App\Models\Message;
use App\Models\Recharge;
use App\Models\TransferLog;
use App\Models\Withdraw;
use App\Services\GamereportService;
use App\Services\TgService;
use App\Services\YesAgent;
use App\Models\AgentInterface;
use App\Models\UserOperateLog;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;  
use App\Models\SystemConfig;
use App\Models\Article;

class IndexController extends Controller
{
    public function __construct()
    {
        try {
            $logo = SystemConfig::getValue('app_logo');
            $url  = '';
            if ($logo) {
                if (strpos($logo, 'http') === 0) {
                    $url = $logo;
                } elseif (strpos($logo, '/') === 0) {
                    // 已是绝对路径（/uploads/xxx.png）
                    $url = env('APP_URL') . $logo;
                } else {
                    // 仅文件名，拼接uploads
                    $url = env('APP_URL') . '/uploads/' . $logo;
                }
            }
            view()->share('app_logo', $url);
        } catch (\Throwable $e) {
            // 防止因数据库/配置异常导致500，降级为空
            \Log::error('Load app_logo failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            view()->share('app_logo', '');
        }
    }
    public function index()
    {
        $user = Auth::user();
        $child = User::getChild($user->id);
        $list = User::whereIn('id',$child)->get();
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $all_recharge = 0;
        $all_withdraw= 0;
        $all_valid_bet= 0;
        $all_win_loss= 0;
        foreach ($list as $k => $v) {
            $all_recharge += User::rechargeSum($v->id,$start,$end); //总存款
            $all_withdraw += User::withdrawSum($v->id,$start,$end); //总提款
            $all_valid_bet += User::vaildBetSum($v->id,$start,$end); //总有效投注
            $all_win_loss += User::totalfanhui($v->id,$start,$end); //总输赢
        }
        // 首页“最新公告”：读取网站后台公告（Article），类别 cateid=6
        $list = Article::where('cateid', 6)
            ->orderBy('id', 'desc')
            ->paginate(6);




        return view('agent.index', compact('user','list','all_recharge','all_withdraw','all_valid_bet','all_win_loss'));
    }

    public function getuserdata(){
        $user = Auth::user();
        $ret = User::getBetDayDta($user->id,6);
        echo json_encode($ret);
    }

    public function notice()
    {
        // 公告列表：网站后台公告（Article）类别 cateid=6
        $list = Article::where('cateid', 6)
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('agent.notice.notice', compact('list'));
    }
    public function message()
    {
        $user = Auth::user();
        // 站内信列表：读取网站后台站内信(messages表)任意类型
        // 可见范围：发给当前代理(user_id=当前) 或 群发给代理(user_id=0 且 isagent=1)
        $list = Message::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) {
                          $q->where('user_id', 0)
                            ->where('isagent', 1);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('agent.notice.message', compact('list'));
    }

    // 站内信详情
    public function messageDetail($id)
    {
        $user = Auth::user();
        $item = Message::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) {
                          $q->where('user_id', 0)
                            ->where('isagent', 1);
                      });
            })
            ->firstOrFail();
        return view('agent.notice.message_detail', compact('item'));
    }
    public function noticeDetail($id)
    {
        // 公告详情：读取 Article 详情
        $item = Article::find($id);
        return view('agent.notice.notice_detail', compact('item'));
    }

    /**
     * 图表
     *
     * @return void
     */
    public function chart()
    {
        return view('agent.report.chart');
    }

    /**
     * 今日概况
     *
     * @return void
     */
    public function todayData()
    {
        $user = Auth::user();
        // 下级会员数
        $child_member = User::getChildMember($user->id);
        $child_member_count = count($child_member);
        // 下级代理
        $child_agent = User::getChildAgent($user->id);
        $child_agent_count = count($child_agent);
        // 直属会员
        $directly_member_count = User::where('pid', $user->id)->where('isagent', 0)->count();
        // 直属代理数
        $directly_agent_count = User::where('pid', $user->id)->where('isagent', 1)->count();
        // 今日新增会员数
        $add_member_count = User::where('pid', $user->id)->whereDate('created_at', date('Y-m-d'))->count();
        // 今日总存款
        $all_child = User::getChild($user->id);
        $all_recharge = Recharge::whereIn('user_id', $all_child)->whereDate('created_at', date('Y-m-d'))->where('state', 2)->sum('amount');
        // 今日总提款
        $all_withdraw = Withdraw::whereIn('user_id', $all_child)->whereDate('created_at', date('Y-m-d'))->where('state', 2)->sum('amount');
        // 今日投注
        $all_bet = GameRecord::whereIn('user_id', $all_child)->whereDate('created_at', date('Y-m-d'))->sum('bet_amount');
        // 今日有效投注
        $all_valid_bet = GameRecord::whereIn('user_id', $all_child)->whereDate('created_at', date('Y-m-d'))->sum('valid_amount');
        // 今日输赢
        $win_loss =  GameRecord::whereIn('user_id', $all_child)->whereDate('created_at', date('Y-m-d'))->sum('win_loss');
        return view('agent.report.today_data',compact('child_member_count','child_agent_count','directly_member_count','directly_agent_count','add_member_count','all_recharge','all_withdraw','all_bet','all_valid_bet','win_loss'));
    }

    /**
     * 盈亏报表
     *
     * @return void
     */
    public function profit(Request $request)
    {
        $data = $request->all();
        $username = $data['username'] ?? '';

        $user = Auth::user();
        $child = User::getChild($user->id);
        //array_push($child,$user->id);
        
        if ($username) {
            $search_user = User::where('username',$username)->first();
            if (!$search_user) {
                return back()->with('opMsg','用户不存在');
            }
            if (!in_array($search_user->id,$child)) {
                return back()->with('opMsg','用户不在您的下级列表中');
            }
        }
        
        $list = User::whereIn('id',$child)->paginate(10);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        foreach ($list as $k => $v) {
            $rechage_times = User::rechargeTimes($v->id,$start,$end); //充值次数
            $withdraw_times = User::withdrawTimes($v->id,$start,$end); //提现次数
            $all_recharge = User::rechargeSum($v->id,$start,$end); //总存款
            $all_withdraw = User::withdrawSum($v->id,$start,$end); //总提款
            $all_valid_bet = User::vaildBetSum($v->id,$start,$end); //总有效投注
            $all_win_loss = User::winLoss($v->id,$start,$end); //总输赢
            $list[$k]->rechage_times = $rechage_times;
            $list[$k]->withdraw_times = $withdraw_times;
            $list[$k]->all_recharge = $all_recharge;
            $list[$k]->all_withdraw = $all_withdraw;
            $list[$k]->all_valid_bet = $all_valid_bet;
            $list[$k]->all_win_loss = $all_win_loss;
        }
        return view('agent.report.profit',compact('list','start','end','username'));
    }


    /**
     * 佣金报表
     *
     * @return void
     */
    public function commission(Request $request)
    {
        $data = $request->all();
        $username = $data['username'] ?? '';
        $user = Auth::user();
        $child = User::getChild($user->id);
        array_push($child,$user->id);
        $lists = User::whereIn('id',$child)->get();
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $rechage_times =0;
        $withdraw_times =0;
        $all_recharge =0;
        $all_withdraw =0;
        $all_valid_bet =0;
        $all_win_loss =0;
        $usersum =0;
        $agentsum =0;
        $all_fanshui = 0;
        $all_redpacket = 0;
        $all_valid_betsum = 0;
        $yongjinsum =0;
        $waityongjinsum = 0;
        foreach ($lists as $k => $v) {
            $rechage_times += User::rechargeTimes($v->id,$start,$end); //充值次数
            $withdraw_times += User::withdrawTimes($v->id,$start,$end); //提现次数
            $all_recharge += User::rechargeSum($v->id,$start,$end); //总存款
            $all_withdraw += User::withdrawSum($v->id,$start,$end); //总提款
            $all_valid_bet += User::vaildBetSum($v->id,$start,$end); //总有效投注
            $all_valid_betsum += User::vaildBetCount($v->id,$start,$end); //总有效投注


            $all_win_loss += User::winLoss($v->id,$start,$end); //总输赢
            $all_fanshui += User::totalfanhui($v->id,$start,$end); //总输赢
            $all_redpacket += User::redpacketSum($v->id,$start,$end); //总输赢

            $usersum += User::UserSum($v->id,$start,$end); //下级会员

            $agentsum += User::AgentSum($v->id,$start,$end); //下级代理

            //$yongjinsum += User::Agentyongjin($v->id,$start,$end); //已结算佣金统计
            
            //$waityongjinsum +=User::Agentyongjinwait($v->id,$start,$end); //未结算佣金统计
        }
        $yongjinsum = TransferLog::where('user_id',$user->id)->where('state',1)->where('transfer_type',999)->sum('yongjin');
        $waityongjinsum = TransferLog::where('user_id',$user->id)->where('state',2)->where('transfer_type',999)->sum('yongjin');

        $list = array();
        $list[0]['username'] = $user->username;
        $list[0]['realname'] = $user->realname;
        $list[0]['isagent'] = $user->isagent;
        $list[0]['rechage_times'] = $rechage_times;
        $list[0]['withdraw_times'] = $withdraw_times;
        $list[0]['all_recharge'] = $all_recharge;
        $list[0]['all_withdraw'] = $all_withdraw;
        $list[0]['all_valid_bet'] = $all_valid_bet;
        $list[0]['all_win_loss'] = $all_win_loss;
        $list[0]['all_fanshui'] = $all_fanshui;
        $list[0]['all_redpacket'] = $all_redpacket;
        $list[0]['all_valid_betsum'] = $all_valid_betsum;

        // $list[0]['usersum'] = $usersum;
        // $list[0]['agentsum'] = $agentsum;
        // $list[0]['yongjinsum'] = $yongjinsum;
        // $list[0]['waityongjinsum'] = $waityongjinsum;
        // $list[0]['rechage_times'] = $rechage_times + User::rechargeTimes($user->id,$start,$end);
        // $list[0]['withdraw_times'] = $withdraw_times+ User::withdrawTimes($user->id,$start,$end);
        // $list[0]['all_recharge'] = $all_recharge+ User::rechargeSum($user->id,$start,$end);
        // $list[0]['all_withdraw'] = $all_withdraw+ User::withdrawSum($user->id,$start,$end);
        // $list[0]['all_valid_bet'] = $all_valid_bet+ User::vaildBetSum($user->id,$start,$end);
        // $list[0]['all_win_loss'] = $all_win_loss+ User::winLoss($user->id,$start,$end);
        // $list[0]['all_fanshui'] = $all_fanshui+ User::totalfanhui($user->id,$start,$end);
        // $list[0]['all_redpacket'] = $all_redpacket+ User::redpacketSum($user->id,$start,$end);
        // $list[0]['all_valid_betsum'] = $all_valid_betsum+ User::vaildBetCount($user->id,$start,$end);
        $list[0]['usersum'] = $usersum;
        $list[0]['agentsum'] = $agentsum;
        $list[0]['yongjinsum'] = $yongjinsum;
        //$list[0]['waityongjinsum'] = $this->getwaityongjinsum($user->id);
        $list[0]['waityongjinsum'] = $waityongjinsum;
        $list = self::arrayToObject($list);

        return view('agent.report.commission',compact('list','start','end','username'));
    }
    
    protected function getwaityongjinsum($user_id)
    {
        $id = $user_id;
        $money = 0;
        $settlementday = intval(SystemConfig::getValue('settlement'));
        $diffday = strtotime(date('Y-m-d'))-$settlementday*60*60*24;
        $val = User::where('isagent','=',1)->where('id','=',$id)->first();
        if ($val){
            $transfermoney = TransferLog::where("state",2)->where('user_id',$val->id)->where('transfer_type',20)->sum('money');
            $money = $transfermoney;

            // $child = User::getChild($val->id);
            // $list = User::whereIn('id',$child)->get();
            // $totalfanhui = 0;
            // $totalredpacketSum =0;
            // $totalRechargeredpacketSum =0;
            // foreach ($list as $k => $v) {
            //     //反水
            //     $totalfanhui += User::totalfanhui($v->id, date('Y-m-d', $diffday) . ' 00:00:00', date('Y-m-d', time()) . ' 23:59:59');
            //     //紅包
            //     $totalredpacketSum +=   User::redpacketSum($v->id, date('Y-m-d', $diffday) . ' 00:00:00', date('Y-m-d', time()) . ' 23:59:59');
            //     // 充值送红包
            //     $totalRechargeredpacketSum +=   User::RechargeredpacketSum($v->id, date('Y-m-d', $diffday) . ' 00:00:00', date('Y-m-d', time()) . ' 23:59:59');
            // }
            // $user = User::where('id',$val->id)->first();
            // $money =  $transfermoney -  $totalfanhui - $totalredpacketSum - $totalRechargeredpacketSum;
        }
        return $money > 0 ? $money : 0;
    }
    
    
    function arrayToObject($e){
        if( gettype($e)!='array' ) return;
        foreach($e as $k=>$v){
            if( gettype($v)=='array' || getType($v)=='object' )
                $e[$k]=(object)self::arrayToObject($v);
        }
        return (object)$e;
    }

    /**
     * 佣金报表
     *
     * @return void
     */
    public function subordinate(Request $request)
    {
        $data = $request->all();
        $username = $data['username'] ?? '';

        $user = Auth::user();
        $child = User::getChild($user->id);
        if ($username) {
            $search_user = User::where('username',$username)->first();
            if (!$search_user) {
                return back()->with('opMsg','用户不存在');
            }
            if (!in_array($search_user->id,$child->toArray())) {
                return back()->with('opMsg','用户不在您的下级列表中');
            }
        }
        $list = User::whereIn('id',$child)->where('isagent',1)->paginate(10);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        foreach ($list as $k => $v) {

            $res = self::agentcommission($v->id,$start,$end);

            $list[$k]->rechage_times = $res['rechage_times'];
            $list[$k]->withdraw_times = $res['withdraw_times'];
            $list[$k]->all_recharge = $res['all_recharge'];
            $list[$k]->all_withdraw = $res['all_withdraw'];
            $list[$k]->all_valid_bet = $res['all_valid_bet'];
            $list[$k]->all_win_loss = $res['all_win_loss'];
            $list[$k]->all_fanshui = $res['all_fanshui'];
            $list[$k]->all_redpacket = $res['all_redpacket'];


            $list[$k]->usersum = User::UserSum($v->id,$start,$end);;
            $list[$k]->agentsum = User::AgentSum($v->id,$start,$end);
            // $list[$k]->yongjinsum = $res['yongjinsum'];
            //$list[$k]->yongjinsum = $this->getwaityongjinsum($v->id);
            $list[$k]->yongjinsum = TransferLog::where('user_id',$v->id)->where('transfer_type',999)->sum('yongjin');
        }
        return view('agent.report.subordinate',compact('list','start','end','username'));
    }



    /**
     * 佣金报表
     *
     * @return void
     */
    public function agentcommission($user_id,$start,$end)
    {

        $child = User::getChild($user_id);
        $lists = User::whereIn('id',$child)->get();
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $rechage_times =0;
        $withdraw_times =0;
        $all_recharge =0;
        $all_withdraw =0;
        $all_valid_bet =0;
        $all_win_loss =0;
        $usersum =0;
        $agentsum =0;
        $all_fanshui=0;
        $all_redpacket = 0;
        foreach ($lists as $k => $v) {
            $rechage_times += User::rechargeTimes($v->id,$start,$end); //充值次数
            $withdraw_times += User::withdrawTimes($v->id,$start,$end); //提现次数
            $all_recharge += User::rechargeSum($v->id,$start,$end); //总存款
            $all_withdraw += User::withdrawSum($v->id,$start,$end); //总提款
            $all_valid_bet += User::vaildBetSum($v->id,$start,$end); //总有效投注
            $all_win_loss += User::winLoss($v->id,$start,$end); //总输赢

            $all_fanshui += User::totalfanhui($v->id,$start,$end); //总输赢
            $all_redpacket += User::redpacketSum($v->id,$start,$end); //总输赢
            //
            $usersum += User::UserSum($v->id,$start,$end); //下级会员

            $agentsum += User::AgentSum($v->id,$start,$end); //下级代理


        }


        $yongjinsum = User::Agentyongjin($user_id,$start,$end); //佣金统计
        $list = array();
        $list['rechage_times'] = $rechage_times;
        $list['withdraw_times'] = $withdraw_times;
        $list['all_recharge'] = $all_recharge;
        $list['all_withdraw'] = $all_withdraw;
        $list['all_valid_bet'] = $all_valid_bet;
        $list['all_win_loss'] = $all_win_loss;
        $list['all_fanshui'] = $all_fanshui;
        $list['all_redpacket'] = $all_redpacket;
        $list['yongjinsum'] = $yongjinsum;
        $list['usersum'] = $yongjinsum;
        $list['agentsum'] = $yongjinsum;


        return $list;
    }

    /**
     * 添加下级会员
     */
    public function addMember(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            if (strlen($data['username']) < 6) return back()->with('opMsg','用户名至少6位');
            $user = User::where('username',$data['username'])->first();
            $puser = Auth::user();
            if ($user) return back()->with('opMsg','用户名已存在');
            
            // 获取当前代理的层级
            $currentAgentLevel = (int)($puser->agent_level ?? 0);
            
            // 根据层级判断是添加代理还是会员
            if ($currentAgentLevel < 2) {
                // 层级小于2，添加的是代理
            $is_agent = 1;
                $allowagent = 1; // 可以发展代理
                $newAgentLevel = $currentAgentLevel + 1; // 新代理的层级是当前层级+1
                
                // 如果新代理的层级等于2，不能再发展代理
                if ($newAgentLevel >= 2) {
                    $allowagent = 0;
                }
                
            $arr = [
                'username' => $data['username'],
                'pid' => $puser->id,
                    'fid' => $puser->id, // 代理的上级代理ID
                'password' => Hash::make($data['password']),
                'realname' => $data['realname'],
                'paypwd' => Hash::make('123456'),
                'vip' => 1,
                    'isagent' => $is_agent,
                    'allowagent' => $allowagent,
                    'agent_level' => $newAgentLevel,
                    'autocode' => $data['autocode'] ?? '',
                    'secretkey' => $data['secretkey'] ?? '',
                    'region_id' => $puser->region_id ?? null // 设置代理所属地区为当前代理的地区
                ];
            } else {
                // 层级大于等于2，添加的是会员
                $is_agent = 0;
                $allowagent = 0;
                $newAgentLevel = 0; // 会员没有代理层级
                
                // 先调用代理接口添加会员
                // 使用当前代理自己的agent_api_id
                $agentApiId = $puser->agent_api_id;
                
                if (!$agentApiId) {
                    return back()->with('opMsg', '无法获取代理接口信息，请联系管理员');
                }
                
                // 获取代理接口信息
                $agentInterface = AgentInterface::find($agentApiId);
                if (!$agentInterface) {
                    return back()->with('opMsg', '代理接口不存在，请联系管理员');
                }
                
                // 使用当前代理自己的autocode和secretkey
                if (!$puser->autocode || !$puser->secretkey) {
                    return back()->with('opMsg', '无法获取API认证信息，请联系管理员');
                }
                
                $autocode = $puser->autocode;
                $secretkey = $puser->secretkey;
                $agentUserName = $puser->username;
                
                // 动态组合Service类名
                $serviceClassName = 'App\\Services\\' . $agentInterface->code;
                if (!class_exists($serviceClassName)) {
                    return back()->with('opMsg', '代理接口类不存在：' . $serviceClassName);
                }
                
                // 调用代理接口添加用户
                try {
                    $agentService = new $serviceClassName($agentUserName, $autocode, $secretkey);
                    
                    // 获取电话（如果有，否则使用默认值）
                    $tel = $data['tel'] ?? $data['phone'] ?? 'N/A';
                    // 备注可以为空
                    $memo = $data['memo'] ?? '';
                    
                    // 准备请求参数
                    $requestParams = [
                        'agentUserName' => $agentUserName,
                        'autocode' => $autocode,
                        'secretkey' => substr($secretkey, 0, 10) . '...', // 只显示前10位，避免完整密钥泄露
                        'userName' => $data['username'],
                        'realname' => $data['realname'],
                        'tel' => $tel,
                        'memo' => $memo,
                        'password' => '***' // 密码不记录到日志
                    ];
                    
                    // 记录请求参数到日志
                    \Log::info('代理接口添加用户请求参数', array_merge($requestParams, ['service_class' => $serviceClassName]));
                    
                    $agentResult = $agentService->addUser(
                        $data['username'],
                        $data['password'],
                        $data['realname'],
                        $tel,
                        $memo
                    );
                    
                    // 记录响应结果到日志
                    \Log::info('代理接口添加用户响应结果', ['result' => $agentResult, 'service_class' => $serviceClassName]);
                    
                    // 检查代理接口返回结果
                    // 统一判断标准：code为0表示成功，其他都是失败
                    $code = $agentResult['code'] ?? -1;
                    
                    if ($code != 0) {
                        $errorMsg = $agentResult['msg'] ?? '代理接口API调用失败';
                        \Log::error('代理接口添加用户失败', [
                            'service_class' => $serviceClassName,
                            'request_params' => $requestParams,
                            'response_result' => $agentResult,
                            'form_data' => $data,
                            'code' => $code
                        ]);
                        return back()->with('opMsg', '添加会员失败：' . $errorMsg);
                    }
                    
                    // 代理接口添加成功，继续添加到本系统
                } catch (\Exception $e) {
                    // 记录异常信息，包括请求参数
                    \Log::error('代理接口添加用户异常', [
                        'service_class' => $serviceClassName ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'request_params' => $requestParams ?? [
                            'agentUserName' => $agentUserName ?? null,
                            'autocode' => $autocode ?? null,
                            'secretkey' => isset($secretkey) ? substr($secretkey, 0, 10) . '...' : null,
                            'userName' => $data['username'] ?? null,
                            'realname' => $data['realname'] ?? null,
                        ],
                        'form_data' => $data
                    ]);
                    return back()->with('opMsg', '添加会员失败：' . $e->getMessage());
                }
                
                $arr = [
                    'username' => $data['username'],
                    'pid' => $puser->id,
                    'password' => Hash::make($data['password']),
                    'apipwd' => $data['password'], // 存入API密码字段
                    'realname' => $data['realname'],
                    'paypwd' => Hash::make('123456'),
                    'vip' => 1,
                    'isagent' => $is_agent,
                    'allowagent' => $allowagent,
                    'agent_level' => $newAgentLevel,
                    'autocode' => '', // 会员不需要AutoCode
                    'secretkey' => '', // 会员不需要SecretKey
                    'region_id' => $puser->region_id ?? null // 设置会员所属地区为当前代理的地区
                ];
            }

            // 如果是添加代理（层级<2），仍然使用TgService注册
            if ($currentAgentLevel < 2) {
            $tg = New TgService;
            // 获取可用的api_code，优先使用'ag'，如果不存在则使用第一个可用的
            $api = \App\Models\Api::where('state', 1)->where('api_code', 'ag')->first();
            if (!$api) {
                $api = \App\Models\Api::where('state', 1)->first();
            }
            
            if (!$api) {
                return back()->with('opMsg','没有可用的游戏平台，请联系管理员');
            }
            
            $result = $tg->register($arr['username'], $data['password'], $api->api_code);
            if ($result['code'] != 200) {
                return back()->with('opMsg',$result['message']);
            }
            }
            
            // 添加到本系统
            User::create($arr);

/*            if($puser->id){
                $puser = User::where('id',$puser->pid)->first();
                $Gamereport = new GamereportService();
                $data['uid'] = $puser->id;
                $data['pid'] = $puser->pid;
                $data['isagent'] = $puser->isagent;
                $data['recnum'] =  1;
                $Gamereport->add($data);
            }*/

             return redirect('/memberlist')->with('opMsg', '添加成功');
        }
        
        // GET请求，显示添加会员/代理表单
        $user = Auth::user();
        $currentAgentLevel = (int)($user->agent_level ?? 0);
        $generatedUsername = null;
        
        // 如果当前代理层级>=2，需要生成随机用户名
        if ($currentAgentLevel >= 2) {
            // 使用当前代理自己的agent_api_id、autocode和secretkey
            $agentApiId = $user->agent_api_id;
            
            // 使用当前代理自己的autocode和secretkey
            if ($user->autocode && $user->secretkey && $agentApiId) {
                $autocode = $user->autocode;
                $secretkey = $user->secretkey;
                $agentUserName = $user->username;
                
                // 获取代理接口信息
                $agentInterface = AgentInterface::find($agentApiId);
                if ($agentInterface) {
                    // 动态组合Service类名
                    $serviceClassName = 'App\\Services\\' . $agentInterface->code;
                    if (class_exists($serviceClassName)) {
                        try {
                            // 准备请求参数
                            $randomUserNameParams = [
                                'agentUserName' => $agentUserName,
                                'autocode' => $autocode,
                                'secretkey' => substr($secretkey, 0, 10) . '...', // 只显示前10位，避免完整密钥泄露
                                'service_class' => $serviceClassName,
                            ];
                            
                            // 记录请求参数到日志
                            \Log::info('代理接口生成随机用户名请求参数', $randomUserNameParams);
                            
                            $agentService = new $serviceClassName($agentUserName, $autocode, $secretkey);
                            $result = $agentService->randomUserName($agentUserName);
                            
                            // 记录响应结果到日志
                            \Log::info('代理接口生成随机用户名响应结果', ['result' => $result, 'service_class' => $serviceClassName]);
                            
                            // 检查API返回结果，code为0表示成功，优先使用playerid作为用户名
                            $code = isset($result['code']) ? (int)$result['code'] : -1;
                            if ($code === 0) {
                                // 优先使用playerid字段，确保转换为字符串以保留前导0
                                if (isset($result['playerid'])) {
                                    $generatedUsername = (string)$result['playerid'];
                                }
                            }
                            
                            // 如果仍然没有获取到用户名，使用备用方式
                            if (!$generatedUsername) {
                                \Log::warning('代理接口生成随机用户名未获取到有效用户名', [
                                    'service_class' => $serviceClassName,
                                    'request_params' => $randomUserNameParams,
                                    'response_result' => $result
                                ]);
                                $generatedUsername = 'user' . time() . rand(1000, 9999);
                            }
                        } catch (\Exception $e) {
                            // 记录异常信息，包括请求参数
                            \Log::error('代理接口生成随机用户名异常', [
                                'service_class' => $serviceClassName ?? 'unknown',
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'request_params' => $randomUserNameParams ?? [
                                    'agentUserName' => $agentUserName ?? null,
                                    'autocode' => $autocode ?? null,
                                    'secretkey' => isset($secretkey) ? substr($secretkey, 0, 10) . '...' : null,
                                ]
                            ]);
                            // 生成失败时，使用备用方式生成随机用户名
                            $generatedUsername = 'user' . time() . rand(1000, 9999);
                        }
                    } else {
                        // 代理接口类不存在，使用备用方式
                        \Log::warning('代理接口类不存在', ['service_class' => $serviceClassName]);
                        $generatedUsername = 'user' . time() . rand(1000, 9999);
                    }
                } else {
                    // 如果没有autocode、secretkey或agent_api_id，使用备用方式生成随机用户名
                    $generatedUsername = 'user' . time() . rand(1000, 9999);
                }
            } else {
                // 如果没有agent_api_id，使用备用方式生成随机用户名
                $generatedUsername = 'user' . time() . rand(1000, 9999);
            }
        }
        
        return view('agent.agent.add_member', compact('generatedUsername'));
    }

    /**
     * 下级会员列表（显示直接下级会员）
     *
     * @param Request $request
     * @return void
     */
    public function memberList(Request $request)
    {
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $currentAgentLevel = (int)($user->agent_level ?? 0);
        
        // 查询直接下级会员（pid等于当前代理id的非代理会员）
        $query = User::where('status', 1)
            ->where('isagent', 0) // 非代理会员
            ->where('pid', $user->id); // 直接下级会员
        
        // 用户名搜索
        if ($username) {
            $query->where('username', 'like', '%' . $username . '%');
        }
        
        $list = $query->orderBy('id', 'desc')->paginate(10);
            
        foreach ($list as $k => $v) {
            $parent = User::find($v->pid);
            $list[$k]->parent = $parent ? $parent->username : '';
        }
        
        return view('agent.agent.member', compact('list', 'user', 'currentAgentLevel'));
    }
    
    /**
     * 全地区会员列表（显示所有地区的非代理会员，可按地区筛选）
     *
     * @param Request $request
     * @return void
     */
    public function regionMemberList(Request $request)
    {
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $regionId = $request->input('region_id') ?? '';
        
        // 获取所有地区列表
        $regions = \Illuminate\Support\Facades\DB::table('regions')
            ->where('status', 1)
            ->get();
            
        // 查询所有地区的非代理会员
        $list = User::where('status', 1)
            ->where('isagent', 0) // 非代理会员
            ->when($regionId, function ($query) use ($regionId) {
                return $query->where('region_id', $regionId); // 按地区筛选
            })
            ->when($username, function ($query) use ($username) {
                return $query->where('username', $username);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        foreach ($list as $k => $v) {
            $parent = User::find($v->pid);
            $list[$k]->parent = $parent ? $parent->username : '';
            
            // 获取地区名称
            if ($v->region_id) {
                $region = \Illuminate\Support\Facades\DB::table('regions')
                    ->where('id', $v->region_id)
                    ->first();
                $list[$k]->region_name = $region ? $region->name : '-';
            } else {
                $list[$k]->region_name = '-';
            }
        }
        
        return view('agent.agent.region_member', compact('list', 'user', 'regions'));
    }

    /**
     * 下级代理列表
     *
     * @param Request $request
     * @return void
     */
    public function agentList(Request $request)
    {
        $user = Auth::user();
        $currentAgentLevel = (int)($user->agent_level ?? 0);
        
        // 如果层级大于等于2，不允许访问
        if ($currentAgentLevel >= 2) {
            return redirect('/')->with('opMsg', '您没有权限访问此页面');
        }
        
        $username = $request->input('username') ?? '';
        $currentRegionId = $user->region_id ?? null;
        
        // 查询条件：状态正常、是代理、在同一地区、层级大于当前代理层级
        $list = User::where('status', 1)
            ->where('isagent', 1)
            ->where('agent_level', '>', $currentAgentLevel) // 层级大于当前代理层级
            ->when($currentRegionId, function ($query) use ($currentRegionId) {
                return $query->where('region_id', $currentRegionId); // 同一地区
            })
            ->when($username, function ($query) use ($username) {
                return $query->where('username', $username);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        foreach ($list as $k => $v) {
            $parent = User::find($v->fid ?? $v->pid);
            $list[$k]->parent = $parent ? $parent->username : '';
            
            // 显示代理层级
            $agentLevel = (int)($v->agent_level ?? 0);
            if ($agentLevel == 0) {
                $list[$k]->level_text = '区域总代理';
            } else {
                $list[$k]->level_text = $agentLevel . '级代理';
            }
            
            // 判断是否可以操作：只要代理层级比当前代理大1就可以操作
            $list[$k]->can_recharge = ($agentLevel == $currentAgentLevel + 1) ? 1 : 0;
        }
        
        return view('agent.agent.agent_list', compact('list', 'user'));
    }

    /**
     * 下注记录
     *
     * @param Request $request
     * @return void
     */
    public function betLog(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $child = User::getChild($user->id);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $list = GameRecord::whereIn('user_id',$child)
            ->when($username,function ($query) use ($username){
                return $query->where('username',$username);
            })->when($start, function ($query) use ($start) {
                $start = date('Y-m-d 00:00:00', strtotime($start));
                return $query->where('created_at', '>', $start);
            })->when($end, function ($query) use ($end) {
                $end = date('Y-m-d 23:59:59', strtotime($end));
                return $query->where('created_at', '<=', $end);
            })->orderBy('id','desc')->paginate(10);
        return view('agent.agent.bet_log',compact('list'));
    }

    /**
     * 充值记录
     *
     * @param Request $request
     * @return void
     */
    public function rechargeLog(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $user_id = User::where('username',$username)->value('id') ?? '';
        $child = User::getChild($user->id);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $list = Recharge::whereIn('user_id',$child)
            ->when($user_id,function ($query) use ($user_id){
                return $query->where('user_id',$user_id);
            })->when($start, function ($query) use ($start) {
                $start = date('Y-m-d 00:00:00', strtotime($start));
                return $query->where('created_at', '>', $start);
            })->when($end, function ($query) use ($end) {
                $end = date('Y-m-d 23:59:59', strtotime($end));
                return $query->where('created_at', '<=', $end);
            })->orderBy('id','desc')->paginate(10);
        return view('agent.agent.recharge_log',compact('list'));
    }

    /**
     * 提现记录
     *
     * @param Request $request
     * @return void
     */
    public function withdrawLog(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $user_id = User::where('username',$username)->value('id') ?? '';
        $child = User::getChild($user->id);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $list = Withdraw::whereIn('user_id',$child)
            ->when($user_id,function ($query) use ($user_id){
                return $query->where('user_id',$user_id);
            })->when($start, function ($query) use ($start) {
                $start = date('Y-m-d 00:00:00', strtotime($start));
                return $query->where('created_at', '>', $start);
            })->when($end, function ($query) use ($end) {
                $end = date('Y-m-d 23:59:59', strtotime($end));
                return $query->where('created_at', '<=', $end);
            })->orderBy('id','desc')->paginate(10);
        return view('agent.agent.recharge_log',compact('list'));
    }

    /**
     * 转账记录
     *
     * @param Request $request
     * @return void
     */
    public function transferLog(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $user_id = User::where('username',$username)->value('id') ?? '';
        $child = User::getChild($user->id);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $list = TransferLog::whereIn('user_id',$child)->whereIn('transfer_type',[0,1])
            ->when($user_id,function ($query) use ($user_id){
                return $query->where('user_id',$user_id);
            })->when($start, function ($query) use ($start) {
                $start = date('Y-m-d 00:00:00', strtotime($start));
                return $query->where('created_at', '>', $start);
            })->when($end, function ($query) use ($end) {
                $end = date('Y-m-d 23:59:59', strtotime($end));
                return $query->where('created_at', '<=', $end);
            })->orderBy('id','desc')->paginate(10);
        return view('agent.agent.transfer_log',compact('list'));
    }


    /**
     * 提现记录
     *
     * @param Request $request
     * @return void
     */
    public function releasewaterLog(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $username = $request->input('username') ?? '';
        $user_id = User::where('username',$username)->value('id') ?? '';
        $child = User::getChild($user->id);
        $start = $data['start'] ?? '';
        $end = $data['end'] ?? '';
        $list = TransferLog::whereIn('user_id',$child)->where('transfer_type',6)
            ->when($user_id,function ($query) use ($user_id){
                return $query->where('user_id',$user_id);
            })->when($start, function ($query) use ($start) {
                $start = date('Y-m-d 00:00:00', strtotime($start));
                return $query->where('created_at', '>', $start);
            })->when($end, function ($query) use ($end) {
                $end = date('Y-m-d 23:59:59', strtotime($end));
                return $query->where('created_at', '<=', $end);
            })->orderBy('id','desc')->paginate(10);
        return view('agent.agent.releasewater_log',compact('list'));
    }
    
    public function generateQrcode()
    {
        $user = Auth::user();
        // 使用智能跳转链接生成二维码（手机端链接，但会自动适配设备）
        $str = env('AGENT_URL')."/promotion?pid=".$user->id."&type=wap";
        // $folder = '/uploads/agent/qrcode';
        // if (!is_dir($folder)) mkdir($folder,0777,true);
        // $filename = $folder.'/'.$user->id.'.png';
        $filename = public_path('uploads/agent/qrcode/'.$user->id.'.png');
        // if (!file_exists($filename)) {
            QrCode::encoding('UTF-8')->format('png')->size(500)->generate($str,$filename); 
        // }
        return response()->download($filename,uniqid().'.png');
        
    }
    
    /**
     * 显示二维码图片
     */
    public function showQrcode()
    {
        $user = Auth::user();
        // 使用智能跳转链接（手机端链接，但会自动适配设备）
        $mobileUrl = env('AGENT_URL')."/promotion?pid=".$user->id."&type=wap";
        
        // 生成二维码并直接返回图片
        $qrcode = QrCode::encoding('UTF-8')
            ->format('png')
            ->size(200)
            ->generate($mobileUrl);
            
        return response($qrcode)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=3600');
    }
    
    /**
     * 根据代理接口获取余额字段名
     * 如果接口是YesAgent，返回yes_money；否则返回null（使用users表的balance字段）
     * 
     * @param string|null $interfaceCode 接口代码
     * @return string|null 余额字段名
     */
    private function getBalanceFieldByInterface($interfaceCode)
    {
        if (empty($interfaceCode)) {
            return null;
        }
        
        // 如果接口code是YesAgent，移除"Agent"然后转小写组合成字段名
        if (stripos($interfaceCode, 'Agent') !== false) {
            $fieldName = str_replace('Agent', '', $interfaceCode);
            $fieldName = strtolower($fieldName);
            return $fieldName . '_money';
        }
        
        return null;
    }
    
    /**
     * 获取用户余额（根据代理接口类型）
     * 
     * @param \App\User $user 用户对象
     * @param string|null $interfaceCode 代理接口代码
     * @return float 余额
     */
    private function getUserBalanceByInterface($user, $interfaceCode)
    {
        $balanceField = $this->getBalanceFieldByInterface($interfaceCode);
        
        if ($balanceField) {
            // 从usersmoney表获取余额
            $usersmoney = \App\Models\Usersmoney::where('user_id', $user->id)->first();
            if ($usersmoney && isset($usersmoney->$balanceField)) {
                return floatval($usersmoney->$balanceField);
            }
            return 0;
        } else {
            // 从users表获取余额
            return floatval($user->balance);
        }
    }
    
    /**
     * 更新用户余额（根据代理接口类型）
     * 
     * @param \App\User $user 用户对象
     * @param float $amount 金额（正数为增加，负数为减少）
     * @param string|null $interfaceCode 代理接口代码
     * @return void
     */
    private function updateUserBalanceByInterface($user, $amount, $interfaceCode)
    {
        $balanceField = $this->getBalanceFieldByInterface($interfaceCode);
        
        if ($balanceField) {
            // 更新usersmoney表的余额
            $usersmoney = \App\Models\Usersmoney::where('user_id', $user->id)->first();
            if (!$usersmoney) {
                $usersmoney = \App\Models\Usersmoney::create(['user_id' => $user->id]);
            }
            
            if (!isset($usersmoney->$balanceField)) {
                $usersmoney->$balanceField = 0;
            }
            
            $usersmoney->$balanceField += $amount;
            $usersmoney->save();
        } else {
            // 更新users表的余额
            $user->balance += $amount;
            $user->save();
        }
    }
    
    //下级充值
    public function recharge(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] < 0) return back()->with('opMsg','请输入正确的金额');
            $user = Auth::user();
            if ($data['amount'] > $user->balance) return back()->with('opMsg','余额不足');
            
            $child = User::find($data['user_id']);
            if (!$child) {
                return back()->with('opMsg', '用户不存在');
            }
            
            // 判断是代理充值还是会员充值
            $isAgentRecharge = $child->isagent == 1;
            
            // 获取来源页面，判断应该返回哪个列表
            $referer = $request->header('referer') ?? '';
            $fromAgentList = false;
            if (strpos($referer, '/agent-list') !== false || strpos($referer, '/agentlist') !== false || strpos($referer, '/agent_list') !== false) {
                $fromAgentList = true;
            }
            // 也可以通过参数判断
            if (isset($data['from']) && $data['from'] == 'agent') {
                $fromAgentList = true;
            }
            
            // 只有给会员充值时才调用接口，给代理充值时直接本地充值
            if (!$isAgentRecharge) {
                // 使用当前代理自己的接口和认证信息
                $agentApiId = $user->agent_api_id;
                
                // 获取代理接口信息
                $agentInterface = null;
                $useYesAgent = true; // 默认使用YesAgent
                
                if ($agentApiId) {
                    $agentInterface = AgentInterface::find($agentApiId);
                    if ($agentInterface && $agentInterface->code) {
                        // 如果接口code是YesAgent，使用YesAgent；否则默认使用YesAgent
                        $useYesAgent = (strtolower($agentInterface->code) === 'yesagent');
                    }
                }
                
                // 如果使用YesAgent，调用代理接口进行充值
                if ($useYesAgent) {
                    // 使用当前代理自己的autocode和secretkey
                    if (!$user->autocode || !$user->secretkey) {
                        // 如果没有认证信息，直接本地充值，不调用接口
                        \Log::warning('代理充值：无法获取API认证信息，跳过接口调用', [
                            'agent_id' => $user->id,
                            'username' => $user->username
                        ]);
                    } else {
                        try {
                            $autocode = $user->autocode;
                            $secretkey = $user->secretkey;
                            $agentUserName = $user->username;
                            
                            // 使用YesAgent进行充值
                            $yesAgent = new YesAgent($agentUserName, $autocode, $secretkey);
                            $orderNo = $child->id . time() . rand(10000, 90000);
                            
                            // 记录请求参数
                            \Log::info('代理接口充值请求', [
                                'agent_username' => $agentUserName,
                                'child_username' => $child->username,
                                'amount' => $data['amount'],
                                'order_no' => $orderNo,
                                'autocode' => substr($autocode, 0, 10) . '...',
                                'has_secretkey' => !empty($secretkey)
                            ]);
                            
                            // 调用接口充值（scoreNum > 0 表示加分）
                            $result = $yesAgent->setServerScore($child->username, $data['amount'], $orderNo);
                            
                            // 记录响应结果
                            \Log::info('代理接口充值响应', [
                                'result' => $result,
                                'child_username' => $child->username,
                                'amount' => $data['amount'],
                                'order_no' => $orderNo
                            ]);
                            
                            // 检查接口返回结果
                            // 统一判断标准：code为0表示成功，其他都是失败
                            $code = $result['code'] ?? -1;
                            
                            if ($code != 0) {
                                $errorMsg = $result['msg'] ?? '代理接口充值失败';
                                \Log::error('代理接口充值失败', [
                                    'result' => $result,
                                    'child_username' => $child->username,
                                    'amount' => $data['amount'],
                                    'order_no' => $orderNo,
                                    'code' => $code,
                                    'error_msg' => $errorMsg
                                ]);
                                return back()->with('opMsg', '代理接口充值失败：' . $errorMsg);
                            }
                            
                            \Log::info('代理接口充值成功', [
                                'child_username' => $child->username,
                                'amount' => $data['amount'],
                                'order_no' => $orderNo
                            ]);
                            
                            // 同步更新usersmoney表的对应字段
                            // 接口代码：YesAgent -> yes_money, PussyAgent -> pussy_money
                            if ($agentInterface && $agentInterface->code) {
                                $interfaceCode = $agentInterface->code;
                                // 更新代理的余额（扣减）
                                \App\Models\Usersmoney::updateBalanceByInterface($user->id, $interfaceCode, -$data['amount']);
                                // 更新会员的余额（增加）
                                \App\Models\Usersmoney::updateBalanceByInterface($child->id, $interfaceCode, $data['amount']);
                            }
                        } catch (\Exception $e) {
                            \Log::error('代理接口充值异常', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'child_username' => $child->username,
                                'amount' => $data['amount']
                            ]);
                            return back()->with('opMsg', '代理接口充值异常：' . $e->getMessage());
                        }
                    }
                }
            } else {
                // 给代理充值，不调用接口，直接本地充值
                \Log::info('代理给下级代理充值，跳过接口调用', [
                    'agent_id' => $user->id,
                    'agent_username' => $user->username,
                    'child_agent_id' => $child->id,
                    'child_agent_username' => $child->username,
                    'amount' => $data['amount']
                ]);
            }
            
            // 本地余额操作
            $user->balance -= $data['amount'];
            $user->save();
            $child->balance += $data['amount'];
            $child->save();
            
            // 创建充值记录
            $arr['order_no'] = $child->id.time().rand(10000,90000);
            $arr['out_trade_no'] = $child->id.time().rand(10000,90000);
            $arr['user_id'] = $child->id;
            $arr['amount'] = $data['amount'];
            $arr['cash_fee'] = 0;
            $arr['real_money'] = $data['amount'];
            $arr['pay_way'] = 11;
            $arr['info'] = '代理充值';
            $arr['state'] = 2;
            Recharge::create($arr);
            
            // 记录操作日志
            try {
                $loginIp = $request->ip();
                $loginUa = $request->header('User-Agent', '');
                $ipAddress = $loginIp; // 可以后续扩展IP地址解析
                $regionId = $user->region_id ?? null;
                $hand = $isAgentRecharge ? 'agent' : null; // 代理充值为agent，会员充值默认
                $desc = '代理充值：给' . ($isAgentRecharge ? '下级代理' : '会员') . $child->username . '充值' . $data['amount'] . '元';
                $info = '充值金额：' . $data['amount'] . '，被充值用户：' . $child->username;
                
                UserOperateLog::insertLog(
                    $user->id,
                    3, // 操作类型 3会员操作
                    $loginUa,
                    $loginIp,
                    $ipAddress,
                    $desc,
                    $info,
                    $regionId,
                    $hand
                );
            } catch (\Exception $e) {
                \Log::error('记录操作日志失败', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id,
                    'child_id' => $child->id
                ]);
            }
            
            // 根据来源页面和充值类型决定返回哪个列表
            // 如果是从代理列表来的，或者充值的对象是代理，返回代理列表
            if ($fromAgentList || $isAgentRecharge) {
                return redirect('/agent-list')->with('opMsg', '充值成功');
            } else {
                // 否则返回会员列表
                return redirect('/memberlist')->with('opMsg', '充值成功');
            }
        }
        $user_id = $request->input('user_id');
        return view('agent.agent.recharge',compact('user_id'));
    }
    
    /**
     * 推广链接跳转 - 根据设备类型自动跳转到对应的链接
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function promotionRedirect(Request $request)
    {
        $pid = $request->input('pid', 0);
        $type = $request->input('type', 'pc'); // pc 或 wap
        
        // 验证pid是否为有效数字
        $pid = intval($pid);
        if ($pid <= 0) {
            // 如果pid无效，跳转到默认注册页面
            $defaultUrl = $this->isMobile() ? env('WAP_URL') : env('PC_URL');
            return redirect($defaultUrl . '/#/register');
        }
        
        // 检测当前设备类型
        $isMobileDevice = $this->isMobile();
        
        // 根据设备类型和链接类型决定跳转目标
        if ($type === 'pc') {
            // 如果是PC端链接
            if ($isMobileDevice) {
                // 手机访问PC链接，跳转到手机端
                $targetUrl = env('WAP_URL') . '/#/register?pid=' . $pid;
            } else {
                // PC访问PC链接，保持PC端
                $targetUrl = env('PC_URL') . '/#/register?pid=' . $pid;
            }
        } else {
            // 如果是手机端链接
            if ($isMobileDevice) {
                // 手机访问手机链接，保持手机端
                $targetUrl = env('WAP_URL') . '/#/register?pid=' . $pid;
            } else {
                // PC访问手机链接，跳转到PC端
                $targetUrl = env('PC_URL') . '/#/register?pid=' . $pid;
            }
        }
        
        return redirect($targetUrl);
    }
}
