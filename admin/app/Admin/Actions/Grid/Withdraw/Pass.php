<?php

namespace App\Admin\Actions\Grid\Withdraw;

use App\Models\UserCard;
use App\Models\Withdraw;
use App\Models\WithdrawLog;
use App\Services\GamereportService;
use App\User;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
        $model = Withdraw::find($id);
        $model->state = 2;
        $model->save();

        return $this->response()
            ->success('审核成功')
            ->refresh();
            
            
            
        $id = $this->getKey();
        $withdraw = Withdraw::find($id);
        $bank_data = UserCard::where("id",$withdraw->card_id)->first();
        $url = 'http://api.fubas.xyz/api/startWithdraw';
        $merchant_id = 'tgdemo';
        //商户密钥
        $api_secret = 'e9afed057f49f46fc7518dd84135d73a';
        $notify_url = env('APP_URL')."/api/fourwaywithdrawnotify";
        //$bank_data = json_decode($platform_type->bank_data,true);
        $data = array(
            'merchantNum'=> $merchant_id,
            'orderNo'=> $withdraw->order_no,
            'withdrawAmount'=> (string)$withdraw->amount,
            'notifyUrl'=>$notify_url,
            'bankCardAccount'=> $bank_data->bank_no,
            'accountHolder'=> $bank_data->bank_owner,
            'openAccountBank'=> $bank_data->bank,
            'moneyPwd'=> '123456',
        );

        $data['sign'] = md5($data['merchantNum'].$data['withdrawAmount'].$data['moneyPwd'].$data['notifyUrl'].$api_secret);
        $headers = array(
            //"Content-Type: application/json",
            "lang: zh-cn",
        );
        \Illuminate\Support\Facades\Log::info("提现发送参数");
        \Illuminate\Support\Facades\Log::info("加密字符串：".$data['merchantNum'].$data['withdrawAmount'].$data['moneyPwd'].$data['notifyUrl'].$api_secret);
        \Illuminate\Support\Facades\Log::info($data);
        \Illuminate\Support\Facades\Log::info(json_encode($data));

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
        if ($result['code']==200){
            $withdraw->state = 2;
            $withdraw->save();
            return $this->response()
                ->success('审核成功')
                ->refresh();
        }else{
            $withdraw->state = 3;
            $withdraw->info = $result['msg'];
            $withdraw->save();
            return $this->response()
                ->error($result['msg'])
                ->refresh();
        }
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
}
