<?php

namespace App\Admin\Actions\Grid\User;
use App\Models\Users;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\Userbalance;
use App\Services\TgService;
use App\User;
use App\Models\TransferLog;
use App\Models\User_Api;
class BackBalance extends RowAction
{
    /**
     * @return string
     */
	protected $title = '一键回收';

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
        $tg = new TgService;
        $user = User::find($id);

        $transferlog = TransferLog::where('user_id', $user->id)->where('transfer_type', 0)->orderBy('id','desc')->first();
		if(!$transferlog){
			return $this->response()->success('没有可回收的金额')->refresh();
		}
		
		$result = $tg->balance($transferlog->api_type,$user->username);
		if($result['code'] != 200){
			return $this->response()->error($result['message'])->refresh();
		}
		if($result['data'] < 1){
			return $this->response()->success('没有可回收的金额')->refresh();
		}
		
		$order_no = date('YmdHis').rand(100000,999999);
		$amount = intval($result['data']);          

		// 使用数据库事务确保数据一致性
		return \DB::transaction(function () use ($user, $transferlog, $tg, $order_no, $amount) {
			$arr = [
				'order_no' => $order_no,
				'api_type' => $transferlog->api_type,
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
			
			$res = $tg->withdrawal($user->username,$amount,$order_no,$transferlog->api_type);
			if($res['code'] != 200){
				return $this->response()->error($res['message'])->refresh();
			} 
			
			$user->balance += $amount;
			$user->save();
			
			$transferlog = TransferLog::where('order_no', $order_no)->first();
			$transferlog->after_money = $user->balance;
			$transferlog->state = 1;
			$transferlog->save();
			
			$User_Api = User_Api::where('api_code',$transferlog->api_type)->where('user_id',$user->id)->first();
			if($User_Api) {
				if($User_Api->api_money <= $amount){
					$User_Api->api_money = 0;
				}else{
					$User_Api->api_money -= $amount;
				}
				$User_Api->save();
			}
			
			return $this->response()->success('回收成功：'.$amount.'元')->refresh();
		});
		
       /* $result = $tg->recoverallbalance($user->username);
        \Illuminate\Support\Facades\Log::info("管理后台一键回收结果".$user->username);

        \Illuminate\Support\Facades\Log::info($result);        
        //$result = json_decode($result,true);
        $blance = 0;
        if($result['code']==0){
            /*foreach ($result['data']['userblance'] as $val){
                if($val['success']=="ok"){
                     $user->AllAccounttranso($val['gamecode'], $val['blance']);
                     //Usersmoney::kouinfo($this->id, $plat_name, $money);
                     $blance +=$val['blance']; 
                }
            }*/

            /*$blance = round($result['data']['userblance'],2);
            if($blance>0){
                return $this->response()->success('共回收金额：'.$blance)->refresh();
            }else{
                return $this->response()->success('没有可回收的金额')->refresh();
            //  return $this->returnMsg(200,'','没有可回收的金额'.$blance);    
            }
             
        }else{
            return $this->response()->success('没有可回收的金额')->refresh();
            //  return $this->returnMsg(500,[],'没有可回收的金额');
        }*/
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
       // return ['你确定要删除此行内容吗？', '弹窗内容'];
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
