<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Hash;
use App\Admin\Actions\Grid\User\Fanyong;
use App\Admin\Tools\AgentFanyong;

use Illuminate\Http\Request;
use Dcat\Admin\Widgets\Modal;

use App\Models\GameRecord;
use App\Models\Withdraw;
use App\Models\Recharge;
class AgentCommissionController extends AdminController
{
    protected $title = '代理佣金报表';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->model()->where('isagent',1);
            // $grid->column('id')->sortable();
            $grid->column('username');
            //$grid->column('结算方案');
            /*$grid->column('child_count','总笔数')->display(function (){
                // return $this->agentbetTimes();
                return \app\User::gamecount($this->id);
            });*/
            // 添加代理层级查看按钮列
            $grid->column('agent_tree', '代理结构')->display('查看')->modal(function ($modal) {
                
                $times = base64_encode(json_encode(request('times')));
                $modal->title($this->username . ' - 代理层级结构-当前代理返佣比例：'. $this->fanshuifee .' %');
                $modal->icon('fa-sitemap');
                $modal->xl(); // 超大模态框
                
                // 使用异步加载内容
                //$url = '/xiao/agent-tree?user_id=' . $this->id;
                $user_url = admin_url('agent-tree?user_id=' . $this->id .'&times='. $times);
                $parent_url = admin_url('agent-tree?times='.$times.'&parent_id=');
                return <<<HTML
<div class="agent-tree-container" data-url="{$user_url}" data-parent_url="{$parent_url}" data-times="{$times}">
    <div class="loading text-center">
        <i class="fa fa-spinner fa-spin"></i> 加载中...
    </div>
</div>
HTML;
});            
            $grid->column('bet_sum','已结算佣金')->display(function (){
                $times = request('times');
                return $this->amountsum_s($this->id,'yongjin',1,$times);
            });
            $grid->column('valid_bet_sum','大约未结算佣金')->display(function (){
                $times = request('times');
                return $this->amountsum_s($this->id,'yongjin',2,$times);
            });
            /*$grid->column('win_loss','总盈利')->display(function (){
                return $this->amountsum_s($this->id,'win_money');
            });
            $grid->column('child_money','总佣金')->display(function (){
                // 使用新的方法计算包含多层级代理的佣金
                return $this->amountsum_s($this->id,'yongjin');
            });			*/
            // $grid->column('win_loss','总获返利')->display(function (){
            //     return \app\User::Agentyongjin2($this->id);
            //     // return $this->agentwinLoss();
            // });
            // $grid->disableActions();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new Fanyong());
                $actions->disableDelete();
                $actions->disableView();
                $actions->disableEdit();
            });
            $grid->disableCreateButton();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('username');
                $filter->between('times','时间')->datetime()->ignore();
            });
            //$grid->tools(new AgentFanyong());
        });
    }
    
    /**
     * 获取代理树数据的接口
     */
    public function agentTree(Request $request)
    {
        $userId = $request->get('user_id', 0);
        $parentId = $request->get('parent_id', 0);
        $times = $request->get('times', 0);

        if ($parentId == 0 && $userId > 0) {
            $parentId = $userId;
        }
        
        $agents = $this->getAgentsByPid($parentId);
        
        if ($agents->isEmpty()) {
            return '<div class="no-data">暂无下级</div>';
        }
        
        $html = '<div class="agent-tree-content">';
        
        foreach ($agents as $agent) {
            $hasChildren = $this->hasChildren($agent->id);
            $typeBadge = $agent->isagent ? 
                '<span class="badge badge-success">代理</span>' : 
                '<span class="badge badge-info">会员</span>';
            
            $toggleBtn = $hasChildren ? 
                '<span class="toggle-btn" data-id="' . $agent->id . '"><i class="fa fa-caret-right"></i></span>' : 
                '<span class="toggle-placeholder"></span>';
            
            $agentInfo = $this->getAgentInfo($agent,$times);
            
            $html .= '<div class="agent-item">';
            $html .= '<div class="agent-node">';
            $html .= $toggleBtn;
            $html .= '<div class="agent-info">';
            $html .= '<div class="agent-name">';
            if($agent->isagent){
                $html .= $typeBadge . ' ' . $agent->username . ' (ID: ' . $agent->id . ')-返佣比例：'. $agent->fanshuifee .' %';
            }else{
                $html .= $typeBadge . ' ' . $agent->username . ' (ID: ' . $agent->id . ')';
            }
            $html .= '</div>';
            $html .= '<div class="agent-stats">';
            
            if ($agent->isagent) {
                // 代理显示完整数据
                $html .= '<span class="stat-item">投注: ' . number_format($agentInfo['bet_sum'], 4) . '</span>';
                $html .= '<span class="stat-item">有效投注: ' . number_format($agentInfo['valid_bet_sum'], 4) . '</span>';
                $html .= '<span class="stat-item">盈利: ' . number_format($agentInfo['win_loss'], 4) . '</span>';
                $html .= '<span class="stat-item">充值: ' . number_format($agentInfo['recharge'], 4) . '</span>';
                $html .= '<span class="stat-item">提款: ' . number_format($agentInfo['withdraws'], 4) . '</span>';
                //$html .= '<span class="stat-item">佣金: ' . number_format($agentInfo['commission'], 2) . '</span>';
            } else {
                // 会员只显示基本数据
                $html .= '<span class="stat-item">投注: ' . number_format($agentInfo['bet_sum'], 4) . '</span>';
                $html .= '<span class="stat-item">有效投注: ' . number_format($agentInfo['valid_bet_sum'], 4) . '</span>';
                $html .= '<span class="stat-item">盈利: ' . number_format($agentInfo['win_loss'], 4) . '</span>';
                $html .= '<span class="stat-item">充值: ' . number_format($agentInfo['recharge'], 4) . '</span>';
                $html .= '<span class="stat-item">提款: ' . number_format($agentInfo['withdraws'], 4) . '</span>';
            }
            
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            
            // 子容器
            $html .= '<div class="agent-children" id="agent-children-' . $agent->id . '"></div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 根据pid获取下级用户（包含代理和会员）
     */
    protected function getAgentsByPid($pid)
    {
        // 根据pid字段查询下级用户
        return \App\Models\User::where('pid', $pid)
            ->orderBy('isagent', 'desc') // 代理排在前面
            ->orderBy('id', 'asc')
            ->get(['id', 'username', 'isagent', 'pid','fanshuifee']);
    }
    
    /**
     * 检查是否有下级用户
     */
    protected function hasChildren($userId)
    {
        return \App\Models\User::where('pid', $userId)->exists();
    }
    
    /**
     * 获取用户信息（包含统计信息）
     */
    protected function getAgentInfo($user,$times)
    {
        $game = $this->amountsum_ss($user->id,'game',$times);
        $recharge = $this->amountsum_ss($user->id,'recharge',$times);
        $withdraws = $this->amountsum_ss($user->id,'withdraws',$times);
        // 如果是普通会员，返回基本信息
        return [
            'name' => $user->username,
            'id' => $user->id,
            'bet_sum' => $game->total_bet_amount,
            'valid_bet_sum' => $game->total_bet_amount,
            'win_loss' => $game->total_win_loss,
            'commission' => 0, // 会员没有佣金
            'recharge' => $recharge->total_amount,
            'withdraws' => $withdraws->total_amount,
        ];
    }
    public static function amountsum_ss($userid,$name,$times)
    {
        $times = json_decode(base64_decode($times),1);

        if($name == 'game'){
            $GameRecord = new GameRecord();
            $GameRecord = $GameRecord->where('user_id', $userid)->where('status',1);
            if($times){
                if(!empty($times['start']) && !empty($times['end'])){
                    $GameRecord = $GameRecord->whereBetween('created_at', [$times['start'], $times['end']]);
                }
            }                
            $stats = $GameRecord->selectRaw('SUM(bet_amount) as total_bet_amount, SUM(win_loss) as total_win_loss')->first();
            return $stats;    
        }
        
        if($name == 'recharge'){
            $Recharge = new Recharge();
            $Recharge = $Recharge->where('user_id', $userid)->where('state',2);
            if($times){
                if(!empty($times['start']) && !empty($times['end'])){
                    $Recharge = $Recharge->whereBetween('created_at', [$times['start'], $times['end']]);
                }
            }            
            $stats = $Recharge->selectRaw('SUM(amount) as total_amount')->first();
            return $stats;    
        }
        
        if($name == 'withdraws'){
            $Withdraw = new Withdraw();
            $Withdraw = $Withdraw->where('user_id', $userid)->where('state',2);
            if($times){
                if(!empty($times['start']) && !empty($times['end'])){
                    $Withdraw = $Withdraw->whereBetween('created_at', [$times['start'], $times['end']]);
                }
            }            
            $stats = $Withdraw->selectRaw('SUM(amount) as total_amount')->first();
            return $stats;    
        }        
    }     
}
