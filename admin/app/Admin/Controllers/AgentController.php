<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use App\Models\AgentSettlement;
use App\Models\AgentInterface;
use App\Models\User as UserModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Admin\Actions\Grid\User\Fanyong;

class AgentController extends AdminController
{
    protected $title = "代理列表";
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->model()->where('isagent', 1);
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('username');
            // $grid->column('password');
            $grid->column('realname','姓名');
            // $grid->column('commisssion','佣金');
            $grid->column('agent_level','代理层级')->display(function($agent_level){
                if ($agent_level == 0) {
                    return '区域总代理';
                }
                return $agent_level ? $agent_level . '级' : '0级';
            });
            $grid->column('region_id','所属地区')->display(function($region_id){
                if ($region_id) {
                    $region = DB::table('regions')->where('id', $region_id)->first();
                    return $region ? $region->name : '-';
                }
                return '-';
            });
            $grid->column('settlement_id','结算方案')->display(function($settlement_id){
                $name = AgentSettlement::find($settlement_id)->name  ?? '';
                return $name;
            });

            $grid->column('fanshuifee','代理返佣(%)')->display(function($fanshuifee){
                return $fanshuifee ? $fanshuifee . '%' : '0%';
            });

            $grid->column('balance','余额');

        
            $grid->column('status','状态')->using([1 => '正常',0 => '禁用']);
            $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('username');

            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new Fanyong());
                $actions->disableDelete();
                $actions->disableView();
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('fid');
            $show->field('username');
            // $show->field('password');
            $show->field('realname');
            $show->field('vip');
            $show->field('level');
            // $show->field('paypwd');
            $show->field('isonline')->using([1 => '在线',0 => '离线']);
            $show->field('isagent')->using([1 => '是',0 => '否']);
            $show->field('allowagent')->using([1 => '是',0 => '否']);
            $show->field('balance');
            $show->field('mbalance');
            $show->field('phone');
            $show->field('mail');
            $show->field('paysum');
            $show->field('status')->using([1 => '正常',0 => '禁用']);
            $show->field('isdel')->using([1 => '是',0 => '否']);
            $show->field('isblack')->using([1 => '是',0 => '否']);
            $show->field('lastip');
            $show->logintime()->as(function ($logintime) {
                return date('Y-m-d H:i:s',$logintime);
            });
            $show->field('sourceurl');
            $show->field('loginsum');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new User(), function (Form $form) {
            $form->display('id');
            
            // 添加地区选择字段（放在最上面）
            if ($form->isCreating()) {
                $regions = DB::table('regions')->where('status', 1)->get();
                $regionOptions = [];
                foreach ($regions as $region) {
                    $regionOptions[$region->id] = $region->name;
                }
                $form->select('region_id', '地区')->options($regionOptions)->required()->help('请先选择地区，然后才能选择该地区的上级代理');
            } else {
                $form->html(function () use ($form) {
                    $regionId = $form->model()->region_id ?? null;
                    if ($regionId) {
                        $region = DB::table('regions')->where('id', $regionId)->first();
                        $regionName = $region ? $region->name : '-';
                    } else {
                        $regionName = '-';
                    }
                    return '<div class="form-group row">
                        <label class="col-sm-2 control-label">所属地区</label>
                        <div class="col-sm-8">
                            <div class="box box-solid box-default no-margin">
                                <div class="box-body">' . $regionName . '</div>
                            </div>
                        </div>
                    </div>';
                });
            }
            
            // 添加父级代理选择字段（放在第二位，根据选择的地区动态加载）
            if ($form->isCreating()) {
                // 获取所有代理数据，包含地区和层级信息
                $allParentAgents = UserModel::where('isagent', 1)->get();
                $allAgentsData = [];
                $agentLevels = [0 => 0]; // 存储代理层级信息，key为代理ID，value为层级
                
                foreach ($allParentAgents as $agent) {
                    $allAgentsData[$agent->id] = [
                        'region_id' => $agent->region_id,
                        'username' => $agent->username,
                        'realname' => $agent->realname,
                        'agent_level' => $agent->agent_level ?? 0
                    ];
                    $agentLevels[$agent->id] = $agent->agent_level ?? 0;
                }
                
                $form->select('fid', '上级代理')->options([0 => '顶级代理'])->default(0)->help('只能选择与所选地区相同的上级代理');
                
                // 添加JavaScript验证和动态加载逻辑
                $allAgentsDataJson = json_encode($allAgentsData);
                $agentLevelsJson = json_encode($agentLevels);
                
                $form->html('
                    <script>
                    $(document).ready(function() {
                        var allAgentsData = ' . $allAgentsDataJson . ';
                        var agentLevels = ' . $agentLevelsJson . ';
                        
                        // 更新上级代理列表的函数
                        function updateParentAgents(regionId) {
                            var $fidSelect = $("select[name=\'fid\']");
                            var currentValue = $fidSelect.val();
                            
                            // 清空并重置选项
                            $fidSelect.empty();
                            $fidSelect.append($("<option></option>").attr("value", "0").text("顶级代理"));
                            
                            if (regionId && regionId != "" && regionId != "0") {
                                // 根据地区过滤代理，排除层级为2的代理（不能再添加下级）
                                $.each(allAgentsData, function(agentId, agentData) {
                                    if (agentData.region_id == regionId && agentLevels[agentId] !== undefined && parseInt(agentLevels[agentId]) < 2) {
                                        var optionText = agentData.username + " (" + agentData.realname + ")";
                                        $fidSelect.append($("<option></option>").attr("value", agentId).text(optionText));
                                    }
                                });
                            }
                            
                            // 如果之前选择的值还存在，则恢复选择
                            if (currentValue && $fidSelect.find("option[value=\'" + currentValue + "\']").length > 0) {
                                $fidSelect.val(currentValue);
                            } else {
                                $fidSelect.val(0);
                            }
                            
                            // 触发change事件以更新Select2（如果使用）
                            $fidSelect.trigger("change");
                        }
                        
                        // 监听地区选择变化
                        setTimeout(function() {
                            $("select[name=\'region_id\']").on("change", function() {
                                var regionId = $(this).val();
                                updateParentAgents(regionId);
                                
                                // 清空上级代理选择
                                $("select[name=\'fid\']").val(0).trigger("change");
                            });
                            
                            // 监听上级代理选择变化
                            $("select[name=\'fid\']").on("change", function() {
                                var selectedId = $(this).val();
                                
                                // 如果是0（顶级代理），不检查
                                if (selectedId == 0 || selectedId == "" || selectedId == null) {
                                    return;
                                }
                                
                                // 检查选中代理的层级
                                if (agentLevels[selectedId] !== undefined && parseInt(agentLevels[selectedId]) >= 2) {
                                    // 提示错误
                                    if (typeof Dcat !== "undefined" && Dcat.error) {
                                        Dcat.error("当前选择的用户已经是最大层级代理（2级），不允许添加下级代理！");
                                    } else if (typeof toastr !== "undefined") {
                                        toastr.error("当前选择的用户已经是最大层级代理（2级），不允许添加下级代理！");
                                    } else {
                                        alert("当前选择的用户已经是最大层级代理（2级），不允许添加下级代理！");
                                    }
                                    
                                    // 清除选择，重置为0（顶级代理）
                                    $(this).val(0).trigger("change");
                                    return false;
                                }
                            });
                            
                            // 页面加载时，如果已经选择了地区，则更新上级代理列表
                            var initialRegionId = $("select[name=\'region_id\']").val();
                            if (initialRegionId) {
                                updateParentAgents(initialRegionId);
                            }
                            
                            // 代理接口字段控制逻辑
                            var $fidSelect = $("select[name=\'fid\']");
                            var $interfaceSelect = $("select[name=\'agent_api_id\']").closest(".form-group");
                            
                            // 控制代理接口字段显示/隐藏的函数
                            function toggleInterfaceField() {
                                var fidValue = $fidSelect.val();
                                if (fidValue && fidValue != "0" && fidValue != "") {
                                    // 选择了上级代理，隐藏代理接口字段
                                    $interfaceSelect.hide();
                                    $interfaceSelect.find("select").removeAttr("required");
                                } else {
                                    // 没有选择上级代理，显示代理接口字段
                                    $interfaceSelect.show();
                                    $interfaceSelect.find("select").attr("required", "required");
                                }
                            }
                            
                            // 监听上级代理选择变化
                            $fidSelect.on("change", function() {
                                toggleInterfaceField();
                            });
                            
                            // 页面加载时初始化
                            toggleInterfaceField();
                        }, 100);
                    });
                    </script>
                ');
            } else {
                // 编辑时也可以修改上级代理
                $currentAgentId = $form->model()->id;
                $currentRegionId = $form->model()->region_id ?? null;
                
                // 获取所有代理数据，排除当前代理及其所有下级代理
                $allParentAgents = UserModel::where('isagent', 1)->where('id', '!=', $currentAgentId)->get();
                $allAgentsData = [];
                $agentLevels = [0 => 0];
                $excludedIds = $this->getDescendantIds($currentAgentId); // 获取所有下级代理ID
                $excludedIds[] = $currentAgentId; // 也排除自己
                
                foreach ($allParentAgents as $agent) {
                    // 排除当前代理及其所有下级代理
                    if (in_array($agent->id, $excludedIds)) {
                        continue;
                    }
                    
                    // 只显示与当前代理相同地区的代理，且层级小于2的代理（2级代理不能再添加下级）
                    $agentLevel = (int)($agent->agent_level ?? 0);
                    if ($agent->region_id == $currentRegionId && $agentLevel < 2) {
                        $allAgentsData[$agent->id] = [
                            'region_id' => $agent->region_id,
                            'username' => $agent->username,
                            'realname' => $agent->realname,
                            'agent_level' => $agentLevel
                        ];
                        $agentLevels[$agent->id] = $agentLevel;
                    }
                }
                
                // 构建选项，排除层级为2的代理（不能再添加下级）
                $fidOptions = [0 => '顶级代理'];
                foreach ($allParentAgents as $agent) {
                    $agentLevel = (int)($agent->agent_level ?? 0);
                    if (!in_array($agent->id, $excludedIds) 
                        && $agent->region_id == $currentRegionId 
                        && $agentLevel < 2) { // 排除层级为2的代理
                        $fidOptions[$agent->id] = $agent->username . ' (' . $agent->realname . ')';
                    }
                }
                
                $currentFid = $form->model()->fid ?? 0;
                $form->select('fid', '上级代理')->options($fidOptions)->default($currentFid)->help('修改上级代理后会自动重新计算代理层级');
                
                // 编辑时显示代理接口（只读）
                $currentInterfaceId = $form->model()->agent_api_id ?? null;
                if ($currentInterfaceId) {
                    $currentInterface = AgentInterface::find($currentInterfaceId);
                    $interfaceName = $currentInterface ? $currentInterface->name : '-';
                    $form->html(function () use ($interfaceName) {
                        return '<div class="form-group row">
                            <label class="col-sm-2 control-label">所属接口</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin">
                                    <div class="box-body">' . $interfaceName . '</div>
                                </div>
                            </div>
                        </div>';
                    });
                }
            }
            
            // 添加代理接口选择字段（仅在创建时，且根据上级代理选择动态显示/隐藏）
            if ($form->isCreating()) {
                $interfaces = AgentInterface::all();
                $interfaceOptions = [];
                foreach ($interfaces as $interface) {
                    $interfaceOptions[$interface->id] = $interface->name;
                }
                $form->select('agent_api_id', '所属接口')
                    ->options($interfaceOptions)
                    ->help('仅总代理（无上级代理）时需要选择，如果选择了上级代理则自动继承上级代理的接口');
            }
            
            // $form->text('fid');
            if ($form->isCreating()) {
                $form->text('username')->rules('required|unique:users',['required' => '请填写用户名','unique' => '用户名重复']);
            } else {
                $form->display('username');
            }
            $form->text('password','密码')->creationRules('required|min:6|max:16',['required' => '请填写密码','min' => '密码最少6位数','max' => '密码最多16位']);
            $form->text('realname','真实姓名')->rules('required',['required' => '请填写真实姓名']);
            // $form->hidden('vip');
            // $form->hidden('level');
            $form->hidden('paypwd');
            // $form->text('isonline');
            // $form->radio('allowagent','允许发展代理')->options([1 => '是',0 => '否'])->default(0); // 已移除，根据层级自动判断
            // $form->text('balance');
            // $form->text('mbalance');
            $form->text('phone','联系电话');
            $form->text('mail','邮箱');
            $form->text('autocode','AutoCode');
            $form->text('secretkey','SecretKey');
            $form->hidden('fanshuifee');
            $form->hidden('agent_level'); // 代理层级字段，在saving回调中计算设置
            // $form->text('paysum');
            $form->radio('status','状态')->options([1 => '正常',0 => '禁用'])->default(1);
            // $form->text('isdel')->options([1 => '是',0 => '否'])->default(0);
            // $form->text('isblack')->options([1 => '是',0 => '否'])->default(0);
            // $form->text('lastip');
            // $form->text('logintime');
            // $form->text('sourceurl');
            $form->hidden('isagent')->default(1);
            $settlements = AgentSettlement::all();
            $options = [];
            foreach ($settlements as $v) {
                $options[$v->id] = $v->name;
            }
            $form->select('settlement_id','结算方案id')->options($options)->required();

            $form->saving(function (Form $form) {
                // 判断是否是新增操作
                $settlementId = (int)($form->settlement_id ?? 0);
                $agent = AgentSettlement::where('id', $settlementId)->first();
                $form->fanshuifee =  $agent->member_fs ;
                
                if ($form->isCreating()) {
                    // 验证是否选择了地区
                    $regionId = (int)($form->region_id ?? 0);
                    if (empty($regionId) || $regionId == 0) {
                        return $form->response()->error('请选择地区，地区为必填项！');
                    }
                    // 确保 region_id 被正确赋值
                    $form->region_id = $regionId;
                    
                    $form->password = Hash::make($form->password);
                    $form->paypwd = $form->paypwd ? Hash::make($form->paypwd) : '';
                    
                    // 计算代理层级 agent_level（内联计算，避免方法调用被拦截）
                    $fid = (int)($form->fid ?? 0);
                    $form->fid = $fid; // 确保 fid 被正确赋值
                    
                    // 处理代理接口逻辑和计算代理层级（优化：只查询一次parent）
                    $agentApiId = (int)($form->agent_api_id ?? 0);
                    $parent = null;
                    $parentAgentLevel = -1; // 初始化为-1，表示没有上级代理
                    
                    if ($fid == 0) {
                        // 总代理（没有上级代理），必须选择代理接口
                        if (empty($agentApiId) || $agentApiId == 0) {
                            return $form->response()->error('总代理必须选择所属代理接口！');
                        }
                        $form->agent_api_id = $agentApiId;
                        // 区域总代理，层级为0
                        $agentLevel = 0;
                    } else {
                        // 选择了上级代理，查询父级信息（只需查询一次）
                        $parent = UserModel::where('id', $fid)->first();
                        if (!$parent) {
                            return $form->response()->error('上级代理不存在！');
                        }
                        
                        // 自动继承上级代理的接口
                        $parentApiId = (int)($parent->agent_api_id ?? 0);
                        if (empty($parentApiId) || $parentApiId == 0) {
                            return $form->response()->error('上级代理未设置代理接口，无法添加下级代理！');
                        }
                        $form->agent_api_id = $parentApiId;
                        
                        // 计算代理层级
                        $parentAgentLevel = (int)($parent->agent_level ?? 0);
                        if ($parentAgentLevel == 0) {
                            $agentLevel = 1;
                        } else {
                            $agentLevel = $parentAgentLevel + 1;
                        }
                    }
                    
                    // 确保 agentLevel 是整数
                    $agentLevel = (int)$agentLevel;
                    
                    // 记录 agentLevel 到日志
                    Log::info('Agent Level Calculation', [
                        'fid' => $fid,
                        'agentLevel' => $agentLevel,
                        'parentAgentLevel' => $parentAgentLevel,
                        'agentLevel_type' => gettype($agentLevel)
                    ]);
                    
                    // 如果选择的上级代理层级是2，强制添加的是会员
                    if ($parentAgentLevel == 2) {
                        $form->isagent = 0;
                        $form->allowagent = 0;
                        $form->agent_level = 0; // 会员没有代理层级
                    } else {
                        // 如果层级大于3，返回错误（区域总代理层级为0，不在限制范围内）
                        if ($agentLevel > 3) {
                            return $form->response()->error('当前代理仅允许添加最大3级，计算出的层级为：' . $agentLevel . '级，添加失败！');
                    }
                    
                    // 确保 agent_level 被正确设置
                    $form->agent_level = (int)$agentLevel;
                    
                    // 记录最终设置的 agent_level
                    Log::info('Agent Level Set', [
                        'agentLevel' => $agentLevel,
                        'form_agent_level' => $form->agent_level,
                        'form_agent_level_type' => gettype($form->agent_level)
                    ]);
                    
                        // 根据层级自动设置是否允许添加代理：层级小于3允许添加，层级等于或大于3不允许添加
                        if ($agentLevel < 3) {
                        $form->allowagent = 1;
                            $form->isagent = 1;
                    } else {
                        $form->allowagent = 0;
                            $form->isagent = 1; // 层级3仍然是代理，只是不能再发展下级代理
                        }
                    }
                } else {
                    // 编辑时，如果修改了上级代理，需要重新计算层级
                    $fid = (int)($form->fid ?? 0);
                    $oldFid = (int)($form->model()->fid ?? 0);
                    
                    // 如果上级代理发生了变化，重新计算层级
                    if ($fid != $oldFid) {
                        // 如果父级ID为0，说明是区域总代理，层级为0
                        if ($fid == 0) {
                            $agentLevel = 0;
                        } else {
                            // 获取直接父级
                            $parent = UserModel::where('id', $fid)->first();
                            
                            if (!$parent) {
                                // 如果找不到父级，说明数据有问题，返回0（区域总代理）
                                $agentLevel = 0;
                            } else {
                                // 获取父级的 agent_level 并转换为整数
                                $parentAgentLevel = (int)($parent->agent_level ?? 0);
                                
                                // 如果父级的 agent_level = 0，那么当前代理是第1级
                                if ($parentAgentLevel == 0) {
                                    $agentLevel = 1;
                                } else {
                                    // 当前代理的层级 = 父级的 agent_level + 1
                                    $agentLevel = $parentAgentLevel + 1;
                                }
                            }
                        }
                        
                        // 确保 agentLevel 是整数
                        $agentLevel = (int)$agentLevel;
                        
                        // 如果层级大于3，返回错误（区域总代理层级为0，不在限制范围内）
                        if ($agentLevel > 3) {
                            return $form->response()->error('当前代理仅允许添加最大3级，计算出的层级为：' . $agentLevel . '级，修改失败！');
                        }
                        
                        // 如果选择的上级代理层级是2，强制添加的是会员
                        if ($parentAgentLevel == 2) {
                            $form->isagent = 0;
                            $form->allowagent = 0;
                            $form->agent_level = 0; // 会员没有代理层级
                            $form->model()->isagent = 0;
                            $form->model()->allowagent = 0;
                            $form->model()->agent_level = 0;
                        } else {
                        // 更新 agent_level - 直接设置到模型和表单
                        $form->agent_level = (int)$agentLevel;
                        $form->model()->agent_level = (int)$agentLevel;
                        
                        Log::info('Agent Level Updated on Edit', [
                            'agent_id' => $form->model()->id,
                            'old_fid' => $oldFid,
                            'new_fid' => $fid,
                            'new_agentLevel' => $agentLevel
                        ]);
                        }
                    } else {
                        // 如果没有修改上级代理，使用原有的层级
                        $agentLevel = (int)($form->model()->agent_level ?? 0);
                        // 也要设置到表单中
                        $form->agent_level = $agentLevel;
                        $form->model()->agent_level = $agentLevel;
                    }
                    
                    $form->password = $form->password ? Hash::make($form->password) : $form->model()->password;
                    $form->paypwd = ($form->paypwd && $form->model()->paypwd != $form->paypwd) ? Hash::make($form->paypwd) : $form->model()->paypwd;
                    
                    // 根据层级自动设置是否允许添加代理：层级小于3允许添加，层级等于或大于3不允许添加
                    if ($agentLevel < 3) {
                        $form->allowagent = 1;
                    } else {
                        $form->allowagent = 0;
                    }
                }

                $form->isagent = 1;
            });
            
            // 使用 saved 回调确保 agent_level 被正确保存
            $form->saved(function (Form $form, $result) {
                // 只有在编辑时才需要检查（创建时在 saving 中已经设置了）
                if (!$form->isCreating() && $result) {
                    // 重新加载模型以获取最新的 fid
                    $model = UserModel::find($form->model()->id);
                    if (!$model) {
                        return;
                    }
                    
                    $fid = (int)($model->fid ?? 0);
                    
                    // 重新计算层级
                    if ($fid == 0) {
                        $agentLevel = 0;
                    } else {
                        $parent = UserModel::where('id', $fid)->first();
                        if ($parent) {
                            $parentAgentLevel = (int)($parent->agent_level ?? 0);
                            if ($parentAgentLevel == 0) {
                                $agentLevel = 1;
                            } else {
                                $agentLevel = $parentAgentLevel + 1;
                            }
                        } else {
                            $agentLevel = 0;
                        }
                    }
                    
                    // 如果层级大于3，不更新（应该在saving中已经验证过）
                    if ($agentLevel <= 3) {
                        // 直接更新数据库
                        $model->agent_level = (int)$agentLevel;
                        $model->save();
                        
                        Log::info('Agent Level Updated in saved callback', [
                            'agent_id' => $model->id,
                            'fid' => $fid,
                            'agentLevel' => $agentLevel
                        ]);
                    }
                }
            });

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
    
    /**
     * 计算代理层级
     * 向上查找父级，直到找到 agent_level = 0 的层级，然后计算当前代理的层级
     * 
     * @param int $fid 父级ID
     * @return int 计算出的代理层级
     */
    private function calculateAgentLevel($fid = 0)
    {
        // 如果父级ID为0，说明是区域总代理，层级为0
        if ($fid == 0) {
            return 0;
        }
        
        // 获取直接父级
        $parent = UserModel::where('id', $fid)->first();
        
        if (!$parent) {
            // 如果找不到父级，说明数据有问题，返回0（区域总代理）
            return 0;
        }
        // 获取父级的 agent_level 并转换为整数
        $parentAgentLevel = (int)($parent->agent_level ?? 0);
        
        // 如果父级的 agent_level = 0，那么当前代理是第1级
        if ($parentAgentLevel == 0) {
            return 1;
        }
        
        // 如果父级的 agent_level 不为 0，需要向上查找，直到找到 agent_level = 0 的层级
        // 计算从 agent_level = 0 到父级的层级数
        $currentFid = (int)($parent->fid ?? 0);
        $maxDepth = 10; // 防止无限循环
        $depth = 0;
        $foundLevel0 = false;
        
        // 向上查找，直到找到 agent_level = 0 的层级
        while ($currentFid > 0 && $depth < $maxDepth) {
            $ancestor = UserModel::where('id', $currentFid)->first();
            
            if (!$ancestor) {
                // 如果找不到祖先，停止查找
                break;
            }
            
            // 如果找到 agent_level = 0 的层级，标记并停止查找
            $ancestorAgentLevel = (int)($ancestor->agent_level ?? 0);
            if ($ancestorAgentLevel == 0) {
                $foundLevel0 = true;
                break;
            }
            
            // 继续向上查找
            $currentFid = (int)($ancestor->fid ?? 0);
            $depth++;
        }
        
        // 如果找到了 agent_level = 0 的层级，或者父级的 agent_level 有效
        // 当前代理的层级 = 父级的 agent_level + 1
        // 确保返回值为整数
        return (int)($parentAgentLevel + 1);
    }
    
    /**
     * 获取指定代理的所有下级代理ID（递归）
     * 
     * @param int $agentId 代理ID
     * @return array 所有下级代理ID数组
     */
    private function getDescendantIds($agentId)
    {
        $descendantIds = [];
        $children = UserModel::where('fid', $agentId)->where('isagent', 1)->get();
        
        foreach ($children as $child) {
            $descendantIds[] = $child->id;
            // 递归获取下级的下级
            $grandChildren = $this->getDescendantIds($child->id);
            $descendantIds = array_merge($descendantIds, $grandChildren);
        }
        
        return $descendantIds;
    }
}

