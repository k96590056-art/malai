<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentSettlement;
use Illuminate\Http\Request;
use App\User;
use App\Models\Syslog;
use App\Models\UserOperateLog;
use App\Services\Lib;
use Illuminate\Support\Facades\Hash;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Auth;
use Session;

class AuthController extends Controller
{
    public function captcha($tmp)
    {
                //生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder;
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();

        //把内容存入session
        Session::put('milkcaptcha', $phrase);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }
    
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $user = User::where('username', $data['name'])->first();
            if (!$user) return back()->with('opMsg', '用户不存在');
            if (!Hash::check($data['password'], $user->password)) return back()->with('opMsg', '密码错误');
            if ($user->isagent != 1) return back()->with('opMsg', '您不是代理，无法进入');
            $user->lastip = $request->getClientIp();
            $user->logintime = time();
            $user->loginsum++;
            $user->isonline = 1; // 设置在线状态
            $user->save();

            // $datas['uid'] = $user->id;
            // $datas['type'] =1;
            // $datas['addtime'] =time();
            // Syslog::create($datas);
            $ip = $request->ip();
           /* $res = Lib::getIpAddress($ip);
            $res = json_decode($res, true);*/
            $ip_address = '';
            /*if ($res['code'] == 200) {
                $ip_address = $res['data']['country'] . $res['data']['province'] . $res['data']['city'];
            }*/
            UserOperateLog::insertLog($user->id, 2, $_SERVER['HTTP_USER_AGENT'], $ip, $ip_address, '代理【' . $user->username . '】登录成功');
            // $remember = $data['remember_me'] ?? 0;
            Auth::login($user);
            return redirect('/');
        }
        return view('agent.auth.login');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $ip = $request->ip();
       // $res = Lib::getIpAddress($ip);
       // $res = json_decode($res, true);
        $ip_address = '';
        /*if ($res['code'] == 200) {
            $ip_address = $res['data']['country'] . $res['data']['province'] . $res['data']['city'];
        }*/
        UserOperateLog::insertLog($user->id, 2, $_SERVER['HTTP_USER_AGENT'], $ip, $ip_address, '会员【' . $user->username . '】注销账号');
        
        // 设置离线状态
        $user->isonline = 0;
        $user->save();
        
        Auth::logout();
        return redirect('/login');
    }

    public function editPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $user = Auth::user();
            if (!Hash::check($data['old_password'], $user->password)) {
                return back()->with('opMsg', '原密码错误');
            }
            $user->password = Hash::make($data['new_password']);
            $user->save();
            return redirect('/')->with('opMsg', '修改密码成功');
        }
        return view('agent.auth.edit_password');
    }


    public function changefanshui(Request $request)
    {
        $uid = $request->input('uid') ?? 0;
        $userinfo = User::where('id', $uid)->first();
        if ($request->isMethod('post')) {
            $user = Auth::user();
            $uid = $request->input('uid') ?? 0;
            $fanshui = $request->input('fanshui') ?? 0;
            if (!$user->allowagent) {
                return back()->with('opMsg', '暂无权限设置');
            }
            if (!$uid || !$fanshui) {
                return back()->with('opMsg', '参数丢失');
            }
            
            // 验证返点比例是否为有效数字
            if (!is_numeric($fanshui) || $fanshui < 0) {
                return back()->with('opMsg', '返点比例必须为有效的正数');
            }



            $userinfo = User::where('id', $uid)->where('status', 1)->first();
            if (!$userinfo) {
                return back()->with('opMsg', '查无次会员');
            }
            $useragentfee = 0;
            if ($user->pid == 0) {
                $usersettlement = AgentSettlement::where('id', $user->settlement_id)->first();
                if (!$usersettlement) {
                    // 如果找不到指定的结算方案，尝试查找状态为1的第一个结算方案
                    $usersettlement = AgentSettlement::where('state', 1)->first();
                    if (!$usersettlement) {
                        return back()->with('opMsg', '代理结算方案不存在，请联系管理员配置');
                    }
                }
                $useragentfee = $usersettlement->member_fs;
            } else {
                $useragentfee = $user->fanshuifee;
            }
            $useragentfee = $user->fanshuifee;

            if($fanshui>$useragentfee){
                return back()->with('opMsg', '返水比例不能大于本级代理返水比例');
            }
            if($fanshui == $useragentfee){
                return back()->with('opMsg', '不允许设置平级代理');
            }
            $userinfo->isagent = 1;
            $userinfo->fanshuifee = $fanshui;
            $userinfo->pid = $user->id; // 设置上级代理ID，建立代理关系
            // 根据当前代理的结算方案来设置下级代理的结算方案
            $userinfo->settlement_id = $user->settlement_id;
            if ($userinfo->save()) {
                return redirect('/memberlist')->with('opMsg', '设置成功');
            } else {
                return back()->with('opMsg', '设置失败');
            }
        }

        return view('agent.auth.setagent', compact("userinfo"));
    }
}
