<?php

namespace App\Admin\Forms;

use App\Models\GameRecord;
use App\Models\Recharge;
use Dcat\Admin\Widgets\Form;
use App\Models\SystemConfig;
use App\Models\Withdraw;
use App\User;
use Illuminate\Http\Request;

class SiteSetting extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        
        foreach ($input as $k => $v) {
            $arr = ['key' => $k,'value' => $v ?? ''];
            SystemConfig::updateOrCreate(['key' => $k],$arr);
        }
        return $this
				->response()
				->success('操作成功')
				->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->tab('网站配置', function () {
            $this->text('site_name','网站名称');
            $this->image('site_logo','网站Logo')->uniqueName();
            $this->image('app_logo','APP Logo')->uniqueName();
            $this->text('site_title','网站标题');
            $this->text('site_keyword','网站关键词');
            $this->radio('cors_enabled','<span style="color: red;">CORS跨域开关</span>')->options([1 => '允许', 0 => '不允许'])->default(1)->help('控制是否允许跨域资源共享，开启后允许其他域名访问API接口，正式运营建议不允许！并输入安全域名！');
            $this->text('safe_domain','安全域名')->help("填入手机端和电脑端域名，多个用,隔开，参考：https://m.baidu.com,https://www.baidu.com");
            $this->radio('redpacket','红包开关')->options([1 => '正常',0 => '关闭'])->default(1);
            $this->radio('fanshui','返水开关')->options([1 => '正常',0 => '关闭'])->default(1);
            $this->radio('site_state','网站状态')->options([1 => '正常',0 => '维护'])->default(1);
            $this->text('repair_tips','网站维护提示语');
            $this->radio('isclose','首页弹窗')->options([1 => '正常',0 => '关闭'])->default(1);
            $this->editor('webcontent','弹窗内容');
        });

                    $this->tab('客服系统配置', function () {
                $this->radio('service_type','服务类型')->options(['kefu' => '客服系统','gongdan' => '工单系统'])->default('kefu')->help('选择使用客服系统或工单系统，只能选择其中一种');
                $this->text('kf_url','客服系统链接')->help('在线客服系统URL地址，仅在选择客服系统时生效');
            });

        $this->tab('APP配置', function () {
            $this->text('android_version','安卓版本号');
            $this->text('android_download_url','安卓下载地址');
            $this->image('android_download_qrcode','安卓下载二维码')->uniqueName();
            $this->text('ios_version','IOS版本号');
            $this->text('ios_download_url','苹果下载地址');
            $this->image('ios_download_qrcode','苹果下载二维码')->uniqueName();
            $this->radio('app_download_switch','APP下载提示框开关')->options([1 => '开启', 0 => '关闭'])->default(1)->help('控制首页是否显示APP下载提示框');
        });
        
        $this->tab('接口设置', function() {
            $this->text('game_api','API接口地址');
            $this->text('merchant_account','api_account');
            $this->text('api_secret','sign_key');
        });
        
        $this->tab('支付设置', function() {
            $this->text('onlinepay_title','网上支付标题');
            $this->text('onlinepay_des','网上支付说明');
            $this->text('companypay_title','公司入款标题');
            $this->text('companypay_des','公司入款说明');


        });
        
        $this->tab('存款设置',function() {
            $this->number('min_recharge_money','最低存款限额');
            $this->text('recharge_fee','充值赠送比例(%)');
            $this->number('max_recharge_money','最高存款限额');
            $this->decimal('min_price','银行卡最低充值金额')->required();
            $this->decimal('max_price','银行卡最大充值金额')->required();
            
        });

        $this->tab('提款设置',function() {
            $this->time('withdraw_begin_time','提款开始时间');
            $this->time('withdraw_end_time','提款结束时间');
            $this->number('daily_withdraw_times','每日可提款次数');
            $this->number('min_withdraw_money','最低提款限额');
            $this->number('max_withdraw_money','最高提款限额');
            $this->text('withdraw_fee','打码量倍数');
            $this->number('min_fanshui_money','最低返水限额');
            $this->decimal('withdraw_cash_fee','USDT-TRC20手续费');
            $this->decimal('withdraw_fee_usdt_erc','USDT-ERC20手续费');
            $this->decimal('withdraw_usdt_rate','提现USDT汇率');
        });

        
        $this->tab('代理设置',function() {
            $this->select('settlement','代理结算周期')->options([1 => 'T+1',2 => 'T+2',3 => 'T+3',4 => 'T+4',5 => 'T+5',6 => 'T+6',7 => 'T+7',10 => 'T+10',15 => 'T+15',20 => 'T+20',30 => 'T+30'])->default(4);
            $this->radio('settlementtypes','代理结算方式')->options([1 => '按输赢结算',0 => '按打码量结算'])->default(1);
            $this->number('settlementlevel','代理返佣级数');
        });
        
        $this->tab('提醒设置', function() {
            /*$this->select('notice_set','提醒方式')->options([1 => '语音加弹窗提醒',2 => '语音提醒',3 => '弹窗提醒'])->default(1);
            $this->radio('auto_refresh','是否自动刷新')->options([0 => '关闭',1 => '开启']);
            $this->number('auto_refresh_interval','刷新时间(秒)');*/			
            $this->file('recharge_apply_audio','充值提醒语音上传')->uniqueName();
            $this->file('withdraw_apply_audio','提款提醒语音上传')->uniqueName();
            $this->file('activity_apply_audio','活动申请语音上传')->uniqueName();
            $this->file('agent_apply_audio','代理申请语音上传')->uniqueName();
            $this->file('work_order_audio','工单提醒语音上传')->uniqueName()->help('当有新工单时播放的提示音');
            // $this->text('syslogday','借呗申请语音上传');
            // $this->text('syslogday','金管家申请语音上传');

        });
        
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [
            'safe_domain'  => SystemConfig::getValue('safe_domain'),
            'site_name'  => SystemConfig::getValue('site_name'),
            'site_logo' => SystemConfig::getValue('site_logo'),
            'app_logo' => SystemConfig::getValue('app_logo'),
            'site_title' => SystemConfig::getValue('site_title'),
            'site_keyword' => SystemConfig::getValue('site_keyword'),
            'kf_url' => SystemConfig::getValue('kf_url'),
            'gongdan_url' => SystemConfig::getValue('gongdan_url'),
            'service_type' => SystemConfig::getValue('service_type'),
            'gongdan_enabled' => SystemConfig::getValue('gongdan_enabled'),
            'kefu_enabled' => SystemConfig::getValue('kefu_enabled'),
            'show_selector' => SystemConfig::getValue('show_selector'),
            'site_state' => SystemConfig::getValue('site_state'),
            'repair_tips' => SystemConfig::getValue('repair_tips'),
            'android_version' => SystemConfig::getValue('android_version'),
            'android_download_url' => SystemConfig::getValue('android_download_url'),
            'android_download_qrcode' => SystemConfig::getValue('android_download_qrcode'),
            'ios_version' => SystemConfig::getValue('ios_version'),
            'ios_download_url' => SystemConfig::getValue('ios_download_url'),
            'ios_download_qrcode' => SystemConfig::getValue('ios_download_qrcode'),
            'app_download_switch' => SystemConfig::getValue('app_download_switch', '1'),
            'game_api' => SystemConfig::getValue('game_api'),
            'merchant_account' => SystemConfig::getValue('merchant_account'),
            'api_secret' => SystemConfig::getValue('api_secret'),
            'withdraw_begin_time' => SystemConfig::getValue('withdraw_begin_time'),
            'withdraw_end_time' => SystemConfig::getValue('withdraw_end_time'),
            'daily_withdraw_times' => SystemConfig::getValue('daily_withdraw_times'),
            'min_withdraw_money' => SystemConfig::getValue('min_withdraw_money'),
            'max_withdraw_money' => SystemConfig::getValue('max_withdraw_money'),
            'min_recharge_money' => SystemConfig::getValue('min_recharge_money'),
            'max_recharge_money' => SystemConfig::getValue('max_recharge_money'),
            'isclose' => SystemConfig::getValue('isclose'),
            'cors_enabled' => SystemConfig::getValue('cors_enabled', '1'),
            'applyday' => SystemConfig::getValue('applyday'),
            'gameorder' => SystemConfig::getValue('gameorder'),
            'syslogday' => SystemConfig::getValue('syslogday'),
            'accountday' => SystemConfig::getValue('accountday'),
            'agentday' => SystemConfig::getValue('agentday'),
            'webcontent' => SystemConfig::getValue('webcontent'),
            'fanshui' => SystemConfig::getValue('fanshui'),
            'redpacket' => SystemConfig::getValue('redpacket'),
            'withdraw_fee' => SystemConfig::getValue('withdraw_fee'),
            'recharge_fee' => SystemConfig::getValue('recharge_fee'),
            'min_fanshui_money' => SystemConfig::getValue('min_fanshui_money'),
            'settlement' => SystemConfig::getValue('settlement'),
            'settlementlevel'=> SystemConfig::getValue('settlementlevel'),
            'notice_set' => SystemConfig::getValue('notice_set'),
            'recharge_apply_audio' => SystemConfig::getValue('recharge_apply_audio'),
            'withdraw_apply_audio' => SystemConfig::getValue('withdraw_apply_audio'),
            'activity_apply_audio' => SystemConfig::getValue('activity_apply_audio'),
            'agent_apply_audio' => SystemConfig::getValue('agent_apply_audio'),
            'work_order_audio' => SystemConfig::getValue('work_order_audio'),
            'settlementtypes' =>SystemConfig::getValue('settlementtypes'),
            'usdt_rate' => SystemConfig::getValue('usdt_rate'),
            'withdraw_cash_fee' => SystemConfig::getValue('withdraw_cash_fee'),
            'withdraw_usdt_rate' => SystemConfig::getValue('withdraw_usdt_rate'),
            'withdraw_fee_usdt_erc' => SystemConfig::getValue('withdraw_fee_usdt_erc'),
            'min_price' => SystemConfig::getValue('min_price'),
            'max_price' => SystemConfig::getValue('max_price'),
            'auto_refresh' => SystemConfig::getValue('auto_refresh'),
            'auto_refresh_interval' => SystemConfig::getValue('auto_refresh_interval'),
            
            // TRON区块链USDT充值配置
            'tron_usdt_address' => SystemConfig::getValue('tron_usdt_address'),
            'tron_api_key' => SystemConfig::getValue('tron_api_key'),
            'tron_api_url' => SystemConfig::getValue('tron_api_url'),
            'tron_confirmations' => SystemConfig::getValue('tron_confirmations'),
            'tron_min_amount' => SystemConfig::getValue('tron_min_amount'),
            'tron_max_amount' => SystemConfig::getValue('tron_max_amount'),
        ];
    }
}
