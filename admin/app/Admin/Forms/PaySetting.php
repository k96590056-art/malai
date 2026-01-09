<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;
use App\Models\SystemConfig;

class PaySetting extends Form
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
            $arr = ['key' => $k,'value' => $v];
            // dd($arr);
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
        // 已移除 ZGPAY 配置页签
        $this->tab('TRC20 USDT充值配置', function () {
            $this->divider('TRON 链上充值');
            $this->text('tron_usdt_address','TRC20-USDT收款地址')->help('TRC20网络USDT收款地址');

            // 开关与密钥输入框（用前端脚本控制显示/隐藏）
            $this->switch('tron_api_key_enabled','启用API Key')
                ->default(0)
                ->help('开启后输入密钥以提高限频，基本无需开启');
            $this->text('tron_api_key','TRC20 API密钥')
                ->help('Tronscan 的 API 密钥，可留空；仅在限频时开启使用，申请地址：https://tronscan.org/#/myaccount/apiKeys/');

            // 前端脚本：根据开关状态显示/隐藏密钥输入框
            $this->html('<script>
                function toggleTronKeyField(){
                    var on = $("input[name=\\"tron_api_key_enabled\\"]").is(":checked");
                    var group = $("input[name=\\"tron_api_key\\"]").closest(".form-group");
                    if(on){ group.show(); } else { group.hide(); }
                }
                $(document).on("change","input[name=\\"tron_api_key_enabled\\"]",toggleTronKeyField);
                $(function(){ toggleTronKeyField(); });
            </script>');

            $this->text('tron_api_url','TRC20 API地址')->default('https://apilist.tronscanapi.com')->help('Tronscan API 地址');
            $this->decimal('tron_exchange_rate','USDT存款汇率')->help('用于按金额换算USDT数量');
            $this->number('tron_confirmations','确认数')->default(12)->help('建议值（按场景）：测试联调：1–2，小额/日常：6，标准生产：12，大额/高风控：20+');
            $this->decimal('tron_min_amount','最小充值金额(USDT)')->default(10);
            $this->decimal('tron_max_amount','最大充值金额(USDT)')->default(50000);
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
            // TRON
            'tron_usdt_address' => SystemConfig::where('key','tron_usdt_address')->value('value') ?? '',
            'tron_api_key' => SystemConfig::where('key','tron_api_key')->value('value') ?? '',
            'tron_api_key_enabled' => (int)(SystemConfig::where('key','tron_api_key_enabled')->value('value') ?? 0),
            'tron_api_url' => SystemConfig::where('key','tron_api_url')->value('value') ?? 'https://apilist.tronscanapi.com',
            'tron_exchange_rate' => SystemConfig::where('key','tron_exchange_rate')->value('value') ?? '',
            'tron_confirmations' => SystemConfig::where('key','tron_confirmations')->value('value') ?? 12,
            'tron_min_amount' => SystemConfig::where('key','tron_min_amount')->value('value') ?? 10,
            'tron_max_amount' => SystemConfig::where('key','tron_max_amount')->value('value') ?? 50000,
        ];
    }
}
