<?php

namespace App\Admin\Forms;

use App\Models\ActivityApply;
use App\Models\GameRecord;
use App\Models\Message;
use App\Models\Recharge;
use App\Models\Suggestion;
use App\Models\SystemConfig;
use App\Models\TransferLog;
use App\Models\UserCard;
use App\Models\UserOperateLog;
use App\Models\Usersmoney;
use App\Models\Withdraw;
use App\Models\WorkOrder;
use App\Models\WorkOrderReply;
use App\User;
use Dcat\Admin\Widgets\Form;

class ClearForm extends Form
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
        $save_days = $input['save_days'] ?? '';
        $time = date('Y-m-d',strtotime("-".$save_days." days"));
        if ($save_days != '') {

        }
        foreach ($input['clear'] as $k => $v) {
            if ($v == "users_table") {
                $uids = User::whereDate('created_at','<',$time)->where('isagent',0)->pluck('id');
                Withdraw::whereIn('user_id',$uids)->delete();
                GameRecord::whereIn('user_id',$uids)->delete();
                Recharge::whereIn('user_id',$uids)->delete();
                UserOperateLog::whereIn('user_id',$uids)->delete();
                TransferLog::whereIn('user_id',$uids)->delete();
                UserCard::whereIn('user_id',$uids)->delete();
                Suggestion::whereIn('user_id',$uids)->delete();
                Message::whereIn('user_id',$uids)->delete();
                Usersmoney::whereIn('user_id',$uids)->delete();
                ActivityApply::whereIn('user_id',$uids)->delete();
                User::whereDate('created_at','<',$time)->where('isagent',0)->delete();
            }
            if ($v == "game_record_table") {
                GameRecord::whereDate('created_at','<',$time)->delete();
            }
            if ($v == "finance_table") {
                Recharge::whereDate('created_at', '<', $time)->delete();
                Withdraw::whereDate('created_at', '<', $time)->delete();
                TransferLog::whereDate('created_at', '<', $time)->delete();
            }
            if ($v == "agent_table") {
                User::whereDate('created_at','<',$time)->where('isagent',1)->delete();
            }
            if ($v == "activity_table") {
                ActivityApply::whereDate('created_at', '<', $time)->delete();
            }
            if ($v == "log_table") {
                UserOperateLog::whereDate('created_at', '<', $time)->delete();
            }
            if ($v == "work_order_table") {
                $workOrderIds = WorkOrder::whereDate('created_at', '<', $time)->pluck('id');
                if ($workOrderIds->isNotEmpty()) {
                    WorkOrderReply::whereIn('work_order_id', $workOrderIds)->forceDelete();
                    WorkOrder::whereIn('id', $workOrderIds)->forceDelete();
                }
            }
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
        $this->confirm('确定要清除吗', '');
        $this->tab('数据清理', function () {
            $options = [
                'users_table' => '会员数据',
                'game_record_table' => '投注数据',
                'finance_table' => '财务数据',
                'agent_table' => '代理数据',
                'activity_table' => '活动数据',
                'log_table' => '日志数据',
                'work_order_table' => '工单数据'
            ];
            $this->checkbox('clear', '数据清理')->options($options);
            // $this->text('users_table','会员数据保留天数');
            // $this->text('game_record_table','投注数据保留天数');
            // $this->text('finance_table','财务数据保留天数');
            // $this->text('syslogday', '代理数据保留天数');
            // $this->text('syslogday', '活动数据保留天数');
            // $this->text('syslogday', '日志数据保留天数');
            $this->number('save_days', '保存天数');
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
            'name'  => 'John Doe',
            'email' => 'John.Doe@gmail.com',
        ];
    }

}
