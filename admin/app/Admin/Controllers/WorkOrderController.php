<?php

namespace App\Admin\Controllers;

use App\Models\WorkOrder;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 工单管理控制器 - 完整功能版本
 */
class WorkOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '工单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        try {
            // 直接使用真实数据模型，强制加载用户关联
            $grid = new Grid(new \App\Models\WorkOrder);
            
            // 强制加载用户关联
            $grid->model()->with('user');
        
        // 工单列表字段 - 只保留最基本的字段
        $grid->column('id', 'ID');
        $grid->column('order_no', '工单编号');
        $grid->column('user.username', '用户账号')->display(function($username) {
            if ($username) {
                return $username;
            }
            // 如果没有关联到用户，显示工单表中的username字段
            return $this->username ?? '未知用户';
        });
        $grid->column('title', '问题');
        $grid->column('category', '分类')->display(function($category) {
            // 使用硬编码映射，避免null引用错误
            $categoryMap = [
                'technical' => '技术问题',
                'payment' => '支付问题',
                'account' => '账户问题',
                'game' => '游戏问题',
                'general' => '一般问题',
                'other' => '其他问题'
            ];
            return $categoryMap[$category] ?? $category;
        });
        $grid->column('priority', '优先级')->display(function($priority) {
            // 使用硬编码映射，避免null引用错误
            $priorityMap = [
                'low' => '低',
                'normal' => '普通',
                'high' => '高',
                'urgent' => '紧急'
            ];
            return $priorityMap[$priority] ?? $priority;
        });
        $grid->column('status', '状态')->display(function($status) {
            // 使用硬编码映射，避免null引用错误
            $statusMap = [
                'pending' => '待处理',
                'processing' => '处理中',
                'replied' => '已回复',
                'closed' => '已关闭'
            ];
            return $statusMap[$status] ?? $status;
        });
        $grid->column('created_at', '创建时间');

        // 操作按钮 - 只保留查看、关闭两个核心功能
        $grid->actions(function ($actions) {
            // 禁用默认的查看、编辑、删除按钮
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            
            // 添加查看按钮（查看工单详情、对话记录和回复功能）
            $actions->append('<a href="' . admin_url('work-orders/' . $actions->getKey()) . '" class="btn btn-xs btn-primary" style="margin-right: 5px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">查看</a>');
            
            // 根据状态添加关闭/开启按钮
            $status = $actions->row->status ?? 'pending';
            if ($status !== 'closed') {
                $actions->append('<button type="button" class="btn btn-xs btn-warning" style="border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onclick="closeWorkOrder(' . $actions->getKey() . ')">关闭</button>');
            } else {
                $actions->append('<button type="button" class="btn btn-xs btn-success" style="border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onclick="openWorkOrder(' . $actions->getKey() . ')">重新开启</button>');
            }
        });

        // 禁用创建按钮
        $grid->disableCreateButton();
        
        // 启用批量删除功能
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete(false);
            });
        });
        
        // 添加JavaScript函数处理工单状态操作
        $grid->header(function () {
            return '<script>
                function closeWorkOrder(id) {
                    if (confirm("确定要关闭这个工单吗？")) {
                        // 创建表单发送POST请求
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = "' . admin_url('work-orders') . '/" + id + "/close";
                        
                        // 添加CSRF令牌
                        const csrfToken = document.createElement("input");
                        csrfToken.type = "hidden";
                        csrfToken.name = "_token";
                        csrfToken.value = "' . csrf_token() . '";
                        form.appendChild(csrfToken);
                        
                        // 提交表单
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
                
                function openWorkOrder(id) {
                    if (confirm("确定要重新开启这个工单吗？")) {
                        // 创建表单发送POST请求
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = "' . admin_url('work-orders') . '/" + id + "/open";
                        
                        // 添加CSRF令牌
                        const csrfToken = document.createElement("input");
                        csrfToken.type = "hidden";
                        csrfToken.name = "_token";
                        csrfToken.value = "' . csrf_token() . '";
                        form.appendChild(csrfToken);
                        
                        // 提交表单
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
                

            </script>';
        });

        } catch (\Exception $e) {
            \Log::error('工单列表加载失败:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // 返回一个简单的错误网格
            $errorGrid = new Grid(new \App\Models\WorkOrder);
            $errorGrid->column('error', '错误信息')->display(function() {
                return '工单列表加载失败，请检查日志';
            });
            return $errorGrid;
        }
        
        return $grid;
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
        // 检查工单是否存在
        $workOrder = \App\Models\WorkOrder::with(['replies', 'user'])->find($id);
        
        // 如果工单不存在或被删除，重定向到工单列表页面
        if (!$workOrder) {
            admin_toastr('工单不存在或已被删除', 'error');
            return redirect(admin_url('work-orders'));
        }
        
        $show = new Show($workOrder);
        
        // 自定义工单详情页的按钮
        $show->tools(function ($tools) use ($workOrder) {
            // 移除默认的编辑按钮和列表按钮
            $tools->disableEdit();
            $tools->disableList();
            $tools->disableDelete();
            
            // 根据工单状态显示开启或关闭按钮
            if ($workOrder->status === 'closed') {
                // 如果工单已关闭，显示重新开启按钮
                $tools->append('<a href="javascript:void(0);" onclick="openWorkOrder(' . $workOrder->id . ')" class="btn btn-sm btn-success" style="margin-right: 8px;">
                    <i class="fa fa-play"></i> 重新开启
                </a>');
            } else {
                // 如果工单未关闭，显示关闭按钮
                $tools->append('<a href="javascript:void(0);" onclick="closeWorkOrder(' . $workOrder->id . ')" class="btn btn-sm btn-warning" style="margin-right: 8px;">
                    <i class="fa fa-stop"></i> 关闭工单
                </a>');
            }

            // 添加删除工单按钮
            $tools->append('<a href="javascript:void(0);" onclick="deleteWorkOrder(' . $workOrder->id . ')" class="btn btn-sm btn-danger" style="margin-right: 8px;">
                <i class="fa fa-trash"></i> 删除工单
            </a>');
            
            // 添加返回列表按钮
            $tools->append('<a href="' . admin_url('work-orders') . '" class="btn btn-sm btn-info">
                <i class="fa fa-list"></i> 返回列表
            </a>');
        });
        
        // 添加JavaScript函数处理工单状态操作
        $show->header(function () {
            return '<script>
                const WORK_ORDER_BASE_URL = "' . admin_url('work-orders') . '";
                const WORK_ORDER_LIST_URL = "' . admin_url('work-orders') . '";
                const CSRF_TOKEN = "' . csrf_token() . '";

                function closeWorkOrder(id) {
                    if (confirm("确定要关闭这个工单吗？")) {
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = WORK_ORDER_BASE_URL + "/" + id + "/close";
                        const csrfToken = document.createElement("input");
                        csrfToken.type = "hidden";
                        csrfToken.name = "_token";
                        csrfToken.value = CSRF_TOKEN;
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
                
                function openWorkOrder(id) {
                    if (confirm("确定要重新开启这个工单吗？")) {
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = WORK_ORDER_BASE_URL + "/" + id + "/open";
                        const csrfToken = document.createElement("input");
                        csrfToken.type = "hidden";
                        csrfToken.name = "_token";
                        csrfToken.value = CSRF_TOKEN;
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                }

                function deleteWorkOrder(id) {
                    if (!confirm("确定要删除这个工单吗？删除后将无法恢复。")) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append("_method", "DELETE");
                    formData.append("_token", CSRF_TOKEN);

                    fetch(WORK_ORDER_BASE_URL + "/" + id, {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.status) {
                            const successMessage = data.message || "工单删除成功";
                            if (typeof Dcat !== "undefined" && typeof Dcat.success === "function") {
                                Dcat.success(successMessage, function () {
                                    window.location.href = WORK_ORDER_LIST_URL;
                                });
                            } else {
                                alert(successMessage);
                                window.location.href = WORK_ORDER_LIST_URL;
                            }
                            return;
                        }

                        const errorMessage = (data && data.message) ? data.message : "工单删除失败，请稍后再试";
                        if (typeof Dcat !== "undefined" && typeof Dcat.error === "function") {
                            Dcat.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    })
                    .catch(error => {
                        console.error("删除工单失败:", error);
                        const fallbackMessage = "删除工单失败: " + error.message;
                        if (typeof Dcat !== "undefined" && typeof Dcat.error === "function") {
                            Dcat.error(fallbackMessage);
                        } else {
                            alert(fallbackMessage);
                        }
                    });
                }
            </script>';
        });
        
        // 工单基本信息 - 统一区域样式（响应式设计）
        $show->field('id', 'ID')->as(function($id) use ($workOrder) {
            return '<style>
                /* 全局响应式样式 - 针对Dcat Admin框架生成的类 */
                @media (max-width: 768px) {
                    /* 工单信息区域响应式 */
                    .work-order-grid {
                        grid-template-columns: 1fr !important;
                        gap: 16px !important;
                    }
                    .work-order-container {
                        padding: 16px !important;
                    }
                    .work-order-header {
                        flex-direction: column !important;
                        align-items: flex-start !important;
                        gap: 12px !important;
                    }
                    .work-order-header .work-order-id {
                        margin-right: 0 !important;
                        margin-bottom: 8px !important;
                    }
                    .work-order-info-card {
                        padding: 12px !important;
                    }
                    .work-order-info-card .info-label {
                        font-size: 13px !important;
                    }
                    .work-order-info-card .info-value {
                        font-size: 14px !important;
                    }
                    
                    /* Dcat Admin框架类响应式 */
                    .show-field.form-group.row {
                        margin-left: 0 !important;
                        margin-right: 0 !important;
                    }
                    .show-field.form-group.row .col-sm-2,
                    .show-field.form-group.row .col-sm-4,
                    .show-field.form-group.row .col-sm-6,
                    .show-field.form-group.row .col-sm-8,
                    .show-field.form-group.row .col-sm-10,
                    .show-field.form-group.row .col-sm-12 {
                        padding-left: 8px !important;
                        padding-right: 8px !important;
                    }
                    .show-field.form-group.row .col-sm-2 {
                        flex: 0 0 100% !important;
                        max-width: 100% !important;
                        margin-bottom: 8px !important;
                    }
                    .show-field.form-group.row .col-sm-4,
                    .show-field.form-group.row .col-sm-6,
                    .show-field.form-group.row .col-sm-8,
                    .show-field.form-group.row .col-sm-10 {
                        flex: 0 0 100% !important;
                        max-width: 100% !important;
                        margin-bottom: 12px !important;
                    }
                    .show-field.form-group.row .col-sm-12 {
                        flex: 0 0 100% !important;
                        max-width: 100% !important;
                    }
                    .show-field.form-group.row label {
                        font-size: 14px !important;
                        margin-bottom: 6px !important;
                    }
                    .show-field.form-group.row .form-control-plaintext {
                        font-size: 14px !important;
                        padding: 8px 0 !important;
                    }
                }
                
                @media (max-width: 480px) {
                    .work-order-container {
                        padding: 12px !important;
                    }
                    .work-order-info-card {
                        padding: 10px !important;
                    }
                    .work-order-header .work-order-id {
                        font-size: 13px !important;
                        padding: 6px 12px !important;
                    }
                    .work-order-header .work-order-time {
                        font-size: 12px !important;
                    }
                    
                    /* 超小屏幕优化 */
                    .show-field.form-group.row {
                        margin-bottom: 16px !important;
                    }
                    .show-field.form-group.row .col-sm-2,
                    .show-field.form-group.row .col-sm-4,
                    .show-field.form-group.row .col-sm-6,
                    .show-field.form-group.row .col-sm-8,
                    .show-field.form-group.row .col-sm-10,
                    .show-field.form-group.row .col-sm-12 {
                        padding-left: 6px !important;
                        padding-right: 6px !important;
                    }
                    .show-field.form-group.row label {
                        font-size: 13px !important;
                        margin-bottom: 4px !important;
                    }
                    .show-field.form-group.row .form-control-plaintext {
                        font-size: 13px !important;
                        padding: 6px 0 !important;
                    }
                }
                
                /* 确保所有show-field在手机端都有合适的间距 */
                .show-field.form-group.row {
                    border-bottom: 1px solid #f0f0f0 !important;
                    padding-bottom: 16px !important;
                    margin-bottom: 16px !important;
                }
                .show-field.form-group.row:last-child {
                    border-bottom: none !important;
                    margin-bottom: 0 !important;
                }
            </style>
            
            <div class="work-order-container" style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="work-order-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f8f9fa;">
                    <div style="display: flex; align-items: center;">
                        <span class="work-order-id" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-right: 12px;">工单 #' . $id . '</span>
                        <span class="work-order-time" style="color: #6c757d; font-size: 14px;">' . date('Y-m-d H:i') . '</span>
                    </div>
                </div>
                
                <div class="work-order-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- 工单编号 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #28a745;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📝 工单编号</div>
                        <div class="info-value" style="color: #28a745; font-family: monospace; font-size: 16px; font-weight: 600;">' . $workOrder->order_no . '</div>
                    </div>
                    
                    <!-- 用户账号 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #6f42c1;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">👤 用户账号</div>
                        <div class="info-value" style="color: #6f42c1; font-weight: 600; font-size: 16px;">' . ($workOrder->username ?? '未知用户') . '</div>
                    </div>
                    
                    <!-- 工单分类 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #1976d2;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">🏷️ 工单分类</div>
                        <span style="background: #e3f2fd; color: #1976d2; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">' . ($workOrder->getCategoryTextAttribute()) . '</span>
                    </div>
                    
                    <!-- 优先级 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #fd7e14;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">⚡ 优先级</div>
                        <span style="background: #fff3cd; color: #856404; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">' . ($workOrder->getPriorityTextAttribute()) . '</span>
                    </div>
                    
                    <!-- 当前状态 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #28a745;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📊 当前状态</div>
                        <span style="background: #d4edda; color: #155724; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">' . ($workOrder->getStatusTextAttribute()) . '</span>
                    </div>
                    
                    <!-- 创建时间 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #6c757d;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📅 创建时间</div>
                        <div class="info-value" style="color: #6c757d; font-family: monospace; font-size: 14px;">' . $workOrder->created_at . '</div>
                    </div>

                    <!-- 工单标题 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #007bff; grid-column: 1 / -1;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📌 工单标题</div>
                        <div class="info-value" style="font-size: 16px; font-weight: 600; color: #495057;">' . htmlspecialchars($workOrder->title) . '</div>
                    </div>
                    
                    <!-- 工单内容 -->
                    <div class="work-order-info-card" style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #007bff; grid-column: 1 / -1;">
                        <div class="info-label" style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">💬 工单内容</div>
                        <div class="info-value" style="line-height: 1.6; color: #495057;">' . nl2br(htmlspecialchars($workOrder->content)) . '</div>
                    </div>
                    
                </div>
            </div>';
        })->unescape();
        
        // 不需要添加其他字段，因为已经在上面统一显示了所有信息
        
        // 对话记录区域
        $replies = \App\Models\WorkOrderReply::where('work_order_id', $workOrder->id)->orderBy('created_at')->get();
        
        // 添加调试日志
        \Log::info('工单对话记录查询:', [
            'work_order_id' => $workOrder->id,
            'replies_count' => $replies->count(),
            'replies_data' => $replies->toArray()
        ]);
        
        if (empty($replies)) {
            $show->field('replies', '对话记录')->as(function() {
                return '<div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div style="text-align: center; color: #6c757d; padding: 40px 20px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">💬</div>
                        <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">暂无对话记录</div>
                        <div style="font-size: 14px; color: #adb5bd;">开始第一段对话吧！</div>
                    </div>
                </div>';
            })->unescape();
        } else {
            $show->field('replies', '对话记录')->as(function () use ($replies) {
                $html = '<style>
                    /* 聊天框响应式样式 */
                    .chat-container { 
                        background: #ffffff; 
                        border: 1px solid #e9ecef; 
                        border-radius: 12px; 
                        padding: 32px; 
                        margin-bottom: 24px; 
                        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                    }
                    .chat-header { 
                        text-align: center; 
                        margin-bottom: 24px; 
                        padding: 16px; 
                        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
                        border-radius: 8px; 
                        border: 1px solid #dee2e6;
                        font-weight: 600;
                        color: #495057;
                        font-size: 16px;
                    }
                    .chat-message { 
                        margin-bottom: 24px; 
                        position: relative;
                    }
                    .chat-message.text-right { text-align: right; }
                    .chat-message.text-left { text-align: left; }
                    .admin-bubble { 
                        display: inline-block; 
                        max-width: 100%; 
                        min-width: 120px;
                        padding: 16px 20px; 
                        border-radius: 20px 20px 4px 20px; 
                        word-wrap: break-word; 
                        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                        color: white; 
                        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
                        position: relative;
                    }
                    .user-bubble { 
                        display: inline-block; 
                        max-width: 100%; 
                        min-width: 120px;
                        padding: 16px 20px; 
                        border-radius: 20px 20px 20px 4px; 
                        word-wrap: break-word; 
                        background: white; 
                        color: #333; 
                        border: 1px solid #e0e0e0;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        position: relative;
                    }
                    .admin-time { 
                        font-size: 12px; 
                        color: #6c757d; 
                        margin-top: 8px; 
                        text-align: right; 
                        font-style: italic;
                    }
                    .user-time { 
                        font-size: 12px; 
                        color: #6c757d; 
                        margin-top: 8px; 
                        text-align: left; 
                        font-style: italic;
                    }
                    .message-avatar {
                        display: inline-block;
                        width: 36px;
                        height: 36px;
                        border-radius: 50%;
                        text-align: center;
                        line-height: 36px;
                        font-size: 16px;
                        font-weight: bold;
                        margin: 0 8px;
                        vertical-align: top;
                    }
                    .admin-avatar {
                        background: #007bff;
                        color: white;
                    }
                    .user-avatar {
                        background: #28a745;
                        color: white;
                    }
                    .message-content {
                        display: inline-block;
                        vertical-align: top;
                        max-width: calc(80% - 50px);
                    }
                    
                    /* 手机端响应式样式 */
                    @media (max-width: 768px) {
                        .chat-container {
                            padding: 20px !important;
                        }
                        .chat-header {
                            padding: 12px !important;
                            font-size: 14px !important;
                        }
                        .admin-bubble,
                        .user-bubble {
                            max-width: 80% !important;
                            min-width: 100px !important;
                            padding: 12px 16px !important;
                            font-size: 14px !important;
                        }
                        .message-content {
                            max-width: calc(80% - 50px) !important;
                        }
                        .message-avatar {
                            width: 32px !important;
                            height: 32px !important;
                            line-height: 32px !important;
                            font-size: 14px !important;
                            margin: 0 6px !important;
                        }
                        .chat-message {
                            margin-bottom: 20px !important;
                        }
                    }
                    
                    @media (max-width: 480px) {
                        .chat-container {
                            padding: 16px !important;
                        }
                        .chat-header {
                            padding: 10px !important;
                            font-size: 13px !important;
                        }
                        .admin-bubble,
                        .user-bubble {
                            max-width: 80% !important;
                            min-width: 80px !important;
                            padding: 10px 14px !important;
                            font-size: 13px !important;
                        }
                        .message-content {
                            max-width: calc(80% - 50px) !important;
                        }
                        .message-avatar {
                            width: 28px !important;
                            height: 28px !important;
                            line-height: 28px !important;
                            font-size: 12px !important;
                            margin: 0 4px !important;
                        }
                        .chat-message {
                            margin-bottom: 16px !important;
                        }
                        .admin-time,
                        .user-time {
                            font-size: 11px !important;
                        }
                    }
                </style>';
                
                $html .= '<div class="chat-container">';
                $html .= '<div class="chat-header">📋 工单对话记录</div>';
                
                    foreach ($replies as $reply) {
                        $isAdmin = $reply->type === 'admin';
                        $alignClass = $isAdmin ? 'text-right' : 'text-left';
                        $bubbleClass = $isAdmin ? 'admin-bubble' : 'user-bubble';
                        $timeClass = $isAdmin ? 'admin-time' : 'user-time';
                        $avatarClass = $isAdmin ? 'admin-avatar' : 'user-avatar';
                        $avatarText = $isAdmin ? 'A' : 'U';
                        
                        // 获取类型文本
                    $typeText = $reply->type === 'admin' ? '客服' : '用户';
                        
                        // 格式化时间
                        $formattedTime = $reply->created_at ? date('m-d H:i', strtotime($reply->created_at)) : '';
                        
                        $html .= '<div class="chat-message ' . $alignClass . '">';
                        
                        if ($isAdmin) {
                            // 管理员消息：头像在右边
                            $html .= '<div class="message-content">';
                            $html .= '<div class="' . $bubbleClass . '">';
                        $html .= '<div style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">' . $typeText . '</div>';
                        $html .= '<div style="line-height: 1.6;">' . nl2br(htmlspecialchars($reply->content)) . '</div>';
                            $html .= '</div>';
                            $html .= '<div class="' . $timeClass . '">' . $formattedTime . '</div>';
                            $html .= '</div>';
                            $html .= '<div class="message-avatar ' . $avatarClass . '">' . $avatarText . '</div>';
                        } else {
                            // 用户消息：头像在左边
                            $html .= '<div class="message-avatar ' . $avatarClass . '">' . $avatarText . '</div>';
                            $html .= '<div class="message-content">';
                            $html .= '<div class="' . $bubbleClass . '">';
                        $html .= '<div style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">' . $typeText . '</div>';
                        $html .= '<div style="line-height: 1.6;">' . nl2br(htmlspecialchars($reply->content)) . '</div>';
                            $html .= '</div>';
                            $html .= '<div class="' . $timeClass . '">' . $formattedTime . '</div>';
                            $html .= '</div>';
                        }
                        
                        $html .= '</div>';
                }
                
                $html .= '</div>';
                
                return $html;
            })->unescape();
        }
        
        // 添加管理员回复区域（响应式设计）
        $show->field('admin_reply', '管理员回复')->as(function() use ($workOrder) {
            $html = '<style>
                /* 管理员回复区域响应式样式 */
                @media (max-width: 768px) {
                    .reply-container {
                        padding: 20px !important;
                    }
                    .reply-header h4 {
                        font-size: 16px !important;
                    }
                    .reply-form-group {
                        margin-bottom: 16px !important;
                    }
                    .reply-form-group label {
                        font-size: 14px !important;
                    }
                    .reply-form-control {
                        padding: 10px !important;
                        font-size: 14px !important;
                    }
                    .reply-submit-btn {
                        padding: 10px 20px !important;
                        font-size: 13px !important;
                    }
                }
                
                @media (max-width: 480px) {
                    .reply-container {
                        padding: 16px !important;
                    }
                    .reply-header h4 {
                        font-size: 15px !important;
                    }
                    .reply-form-group {
                        margin-bottom: 14px !important;
                    }
                    .reply-form-group label {
                        font-size: 13px !important;
                    }
                    .reply-form-control {
                        padding: 8px !important;
                        font-size: 13px !important;
                    }
                    .reply-submit-btn {
                        padding: 8px 16px !important;
                        font-size: 12px !important;
                        width: 100% !important;
                    }
                    .reply-submit-container {
                        text-align: center !important;
                    }
                }
            </style>';
            
            $html .= '<div class="reply-container" style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">';
            $html .= '<div class="reply-header" style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f8f9fa;">';
            $html .= '<h4 style="margin: 0; color: #495057; font-size: 18px; font-weight: 600;">💬 管理员回复</h4>';
            $html .= '</div>';
            
            // 回复表单
            $html .= '<form id="replyForm" action="' . admin_url('work-orders/' . $workOrder->id . '/reply') . '" method="POST" style="margin-top: 20px;">';
            $html .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
            
            // 回复内容输入框
            $html .= '<div class="reply-form-group" style="margin-bottom: 20px;">';
            $html .= '<label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">回复内容 *</label>';
            $html .= '<textarea name="admin_reply" id="admin_reply" rows="4" class="reply-form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="请输入您的回复内容" required></textarea>';
            $html .= '</div>';
            
            // 状态选择
            $html .= '<div class="reply-form-group" style="margin-bottom: 20px;">';
            $html .= '<label style="display: block; margin-bottom: 8px; font-weight: 600; color: #495057;">更新状态</label>';
            $html .= '<select name="status" id="status" class="reply-form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">';
            $html .= '<option value="replied" ' . ($workOrder->status === 'replied' ? 'selected' : '') . '>已回复</option>';
            $html .= '<option value="processing" ' . ($workOrder->status === 'processing' ? 'selected' : '') . '>处理中</option>';
            $html .= '<option value="closed" ' . ($workOrder->status === 'closed' ? 'selected' : '') . '>已关闭</option>';
            $html .= '</select>';
            $html .= '</div>';
            
            // 提交按钮
            $html .= '<div class="reply-submit-container" style="text-align: right;">';
            $html .= '<button type="submit" class="reply-submit-btn" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 8px rgba(0,123,255,0.3);">提交回复</button>';
            $html .= '</div>';
            
            $html .= '</form>';
            
            // 添加JavaScript处理表单提交
            $html .= '<script>
                document.getElementById("replyForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector("button[type=submit]");
                    const originalText = submitBtn.textContent;
                    
                    // 显示提交中状态
                    submitBtn.textContent = "提交中...";
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = "0.7";
                    
                    // 使用XMLHttpRequest替代fetch，避免Promise链问题
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", this.action, true);
                    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            // 恢复按钮状态
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = "1";
                            
                            if (xhr.status === 200) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.status) {
                                        // 显示成功消息
                                        Dcat.success("回复提交成功！");
                                        // 清空表单
                                        document.getElementById("admin_reply").value = "";
                                        // 刷新页面显示最新回复
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1500);
                                    } else {
                                        Dcat.error("回复提交失败：" + (response.message || "未知错误"));
                                    }
                                } catch (e) {
                                    console.error("JSON解析失败:", e);
                                    Dcat.error("响应格式错误，请检查服务器日志");
                                }
                            } else {
                                console.error("HTTP错误:", xhr.status, xhr.statusText);
                                Dcat.error("提交失败：HTTP " + xhr.status + " - " + xhr.statusText);
                            }
                        }
                    };
                    
                    xhr.onerror = function() {
                        console.error("网络错误");
                        Dcat.error("网络错误，请检查网络连接");
                        // 恢复按钮状态
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = "1";
                    };
                    
                    xhr.send(formData);
                });
            </script>';
            
            $html .= '</div>';
            
            return $html;
        })->unescape();
        
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $id = request('id');
        $workOrder = null;
        
        if ($id) {
            // 直接使用真实数据模型，强制加载用户关联
            $workOrder = \App\Models\WorkOrder::with('user')->find($id);
            
            if (!$workOrder) {
                admin_toastr('工单不存在', 'error');
                return redirect(admin_url('work-orders'));
            }
        }
        
        $form = new Form($workOrder);
        
        if ($workOrder) {
            $form->display('id', 'ID');
            $form->display('order_no', '工单编号');
            $form->display('user_account', '用户账号');
            $form->display('title', '标题');
            $form->display('content', '内容');
            $form->display('category', '分类');
            $form->display('priority', '优先级');
            $form->display('status', '状态');
            $form->display('created_at', '创建时间');
            
            // 管理员回复字段
            $form->textarea('admin_reply', '管理员回复')->rows(4);
            $form->datetime('admin_reply_time', '回复时间')->default(date('Y-m-d H:i:s'));
            
            // 状态更新
            $form->select('status', '更新状态')->options([
                'pending' => '待处理',
                'processing' => '处理中',
                'replied' => '已回复',
                'closed' => '已关闭',
            ])->default('replied');
            
            // 表单保存前处理
            $form->saving(function (Form $form) use ($workOrder) {
                if ($form->admin_reply && !$form->admin_reply_time) {
                    $form->admin_reply_time = now();
                }
            });
        }

        return $form;
    }

    /**
     * 工单回复页面 - 对话模式
     */
    public function reply($id)
    {
        // 检查工单是否存在
        $workOrder = \App\Models\WorkOrder::with(['replies', 'user'])->find($id);
        
        // 如果工单不存在或被删除，重定向到工单列表页面
        if (!$workOrder) {
            admin_toastr('工单不存在或已被删除', 'error');
            return redirect(admin_url('work-orders'));
        }
        
        // 获取工单回复记录
        $replies = \App\Models\WorkOrderReply::where('work_order_id', $workOrder->id)->orderBy('created_at')->get();
        
        $form = new Form();
        $form->action(admin_url('work-orders/' . $id . '/reply'));
        $form->method('POST');
        
        // 禁用编辑按钮，只保留回复功能
        $form->disableEditingCheck();
        $form->disableDeleteButton();
        $form->disableViewButton();
        
        // 工单基本信息 - 对话模式头部
        $form->html('<div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f8f9fa;">
                <div style="display: flex; align-items: center;">
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-right: 12px;">工单 #' . $workOrder->id . '</span>
                    <span style="color: #6c757d; font-size: 14px;">' . date('Y-m-d H:i') . '</span>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- 工单编号 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📝 工单编号</div>
                    <div style="color: #28a745; font-family: monospace; font-size: 16px; font-weight: 600;">' . $workOrder->order_no . '</div>
                </div>
                
                <!-- 用户账号 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #6f42c1;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">👤 用户账号</div>
                    <div style="color: #6f42c1; font-weight: 600; font-size: 16px;">' . ($workOrder->username ?? '未知用户') . '</div>
                </div>
                
                <!-- 工单标题 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #007bff; grid-column: 1 / -1;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📌 工单标题</div>
                    <div style="font-size: 16px; font-weight: 600; color: #495057;">' . htmlspecialchars($workOrder->title) . '</div>
                </div>
                
                <!-- 工单内容 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #007bff; grid-column: 1 / -1;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">💬 工单内容</div>
                    <div style="line-height: 1.6; color: #495057;">' . nl2br(htmlspecialchars($workOrder->content)) . '</div>
                </div>
                
                <!-- 工单分类 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #1976d2;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">🏷️ 工单分类</div>
                    <span style="background: #e3f2fd; color: #1976d2; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">' . ($workOrder->getCategoryTextAttribute()) . '</span>
                </div>
                
                <!-- 优先级 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #fd7e14;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">⚡ 优先级</div>
                    <span style="background: #fff3cd; color: #856404; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">' . ($workOrder->getPriorityTextAttribute()) . '</span>
                </div>
                
                <!-- 当前状态 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📊 当前状态</div>
                    <span style="background: #d4edda; color: #155724; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">' . ($workOrder->getStatusTextAttribute()) . '</span>
                </div>
                
                <!-- 创建时间 -->
                <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #6c757d;">
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">📅 创建时间</div>
                    <div style="color: #6c757d; font-family: monospace; font-size: 14px;">' . $workOrder->created_at . '</div>
                </div>
            </div>
        </div>');
        
                        // 对话记录区域
                if (empty($replies)) {
                    $form->html('<div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div style="text-align: center; color: #6c757d; padding: 40px 20px;">
                            <div style="font-size: 48px; margin-bottom: 16px;">💬</div>
                            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">暂无对话记录</div>
                            <div style="font-size: 14px; color: #adb5bd;">开始第一段对话吧！</div>
                        </div>
                    </div>');
                } else {
                    $chatHtml = '<div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <style>
                            /* 确保聊天内容正常横排显示 */
                            .chat-content {
                                white-space: pre-wrap !important;
                                word-break: break-word !important;
                                overflow-wrap: break-word !important;
                                text-align: left !important;
                                direction: ltr !important;
                                writing-mode: horizontal-tb !important;
                                display: block !important;
                                width: 100% !important;
                            }
                            /* 聊天消息容器样式 */
                            .chat-message {
                                display: flex !important;
                                align-items: flex-start !important;
                                margin-bottom: 20px !important;
                                max-width: 100% !important;
                            }
                            .chat-message.user {
                                justify-content: flex-start !important;
                            }
                            .chat-message.admin {
                                justify-content: flex-end !important;
                            }
                            /* 消息气泡样式 */
                            .message-bubble {
                                max-width: 70% !important;
                                min-width: 200px !important;
                                padding: 16px 20px !important;
                                border-radius: 20px !important;
                                line-height: 1.6 !important;
                                word-wrap: break-word !important;
                                white-space: pre-wrap !important;
                                overflow-wrap: break-word !important;
                                text-align: left !important;
                                direction: ltr !important;
                                writing-mode: horizontal-tb !important;
                            }
                            .message-bubble.user {
                                background: white !important;
                                color: #333 !important;
                                border: 1px solid #e0e0e0 !important;
                                box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
                                border-radius: 20px 20px 20px 4px !important;
                            }
                            .message-bubble.admin {
                                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
                                color: white !important;
                                box-shadow: 0 4px 12px rgba(0,123,255,0.3) !important;
                                border-radius: 20px 20px 4px 20px !important;
                            }
                            /* 头像样式 */
                            .chat-avatar {
                                width: 36px !important;
                                height: 36px !important;
                                border-radius: 50% !important;
                                text-align: center !important;
                                line-height: 36px !important;
                                font-size: 16px !important;
                                font-weight: bold !important;
                                margin: 0 8px !important;
                                flex-shrink: 0 !important;
                            }
                            .chat-avatar.user {
                                background: #28a745 !important;
                                color: white !important;
                            }
                            .chat-avatar.admin {
                                background: #007bff !important;
                                color: white !important;
                            }
                            /* 时间样式 */
                            .chat-time {
                                font-size: 12px !important;
                                color: #6c757d !important;
                                margin-top: 8px !important;
                                font-style: italic !important;
                            }
                            .chat-time.user {
                                text-align: left !important;
                            }
                            .chat-time.admin {
                                text-align: right !important;
                            }
                        </style>
                        <div style="text-align: center; margin-bottom: 24px; padding: 16px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; border: 1px solid #dee2e6; font-weight: 600; color: #495057; font-size: 16px;">
                            📋 工单对话记录
                        </div>';
                    
                    foreach ($replies as $reply) {
                        $isAdmin = $reply->type === 'admin';
                        $typeText = $reply->type === 'admin' ? '客服' : '用户';
                        $formattedTime = $reply->created_at ? date('m-d H:i', strtotime($reply->created_at)) : '';
                        
                        if ($isAdmin) {
                            // 管理员消息：头像在右边
                            $chatHtml .= '<div class="chat-message admin">
                                <div style="flex: 1; text-align: right;">
                                    <div class="message-bubble admin">
                                        <div style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">' . $typeText . '</div>
                                        <div class="chat-content">' . nl2br(htmlspecialchars($reply->content)) . '</div>
                                    </div>
                                    <div class="chat-time admin">' . $formattedTime . '</div>
                                </div>
                                <div class="chat-avatar admin">A</div>
                            </div>';
                        } else {
                            // 用户消息：头像在左边
                            $chatHtml .= '<div class="chat-message user">
                                <div class="chat-avatar user">U</div>
                                <div style="flex: 1;">
                                    <div class="message-bubble user">
                                        <div style="margin-bottom: 8px; font-weight: 600; font-size: 13px;">' . $typeText . '</div>
                                        <div class="chat-content">' . nl2br(htmlspecialchars($reply->content)) . '</div>
                                    </div>
                                    <div class="chat-time user">' . $formattedTime . '</div>
                                </div>
                            </div>';
                        }
                    }
                    
                    $chatHtml .= '</div>';
                    $form->html($chatHtml);
                }
        
        // 管理员回复区域
        $form->html('<div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f8f9fa;">
                <h4 style="margin: 0; color: #495057; font-size: 18px; font-weight: 600;">💬 管理员回复</h4>
            </div>
        </div>');
        
        // 管理员回复字段
        $form->textarea('admin_reply', '回复内容')->rows(4)->required()->help('请输入您的回复内容');
        $form->datetime('admin_reply_time', '回复时间')->default(date('Y-m-d H:i:s'));
        
        // 状态更新
        $form->select('status', '更新状态')->options([
            'pending' => '待处理',
            'processing' => '处理中',
            'replied' => '已回复',
            'closed' => '已关闭',
        ])->default('replied')->help('选择工单的新状态');
        
        // 表单保存前处理
        $form->saving(function (Form $form) use ($workOrder) {
            if ($form->admin_reply && !$form->admin_reply_time) {
                $form->admin_reply_time = now();
            }
        });
        
        // 表单保存后处理
        $form->saved(function (Form $form) use ($workOrder) {
            $adminReply = $form->admin_reply;
            $status = $form->status;
            $adminReplyTime = $form->admin_reply_time;
            
            if ($adminReply) {
                try {
                    // 获取当前登录的管理员信息
                    $adminId = \Admin::user()->id ?? 0;
                    
                    // 保存到回复表
                    $reply = \App\Models\WorkOrderReply::create([
                        'work_order_id' => $workOrder->id,
                        'user_id' => null, // 管理员回复，user_id为null
                        'admin_id' => $adminId, // 使用当前登录的管理员ID
                        'content' => $adminReply,
                        'type' => 'admin'
                    ]);
                    
                    \Log::info('工单回复记录创建成功:', $reply->toArray());
                } catch (\Exception $e) {
                    \Log::error('工单回复记录创建失败:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            try {
                // 更新工单状态
                $workOrder->update([
                    'status' => $status,
                    'admin_reply' => $adminReply,
                    'admin_reply_time' => $adminReplyTime
                ]);
                
                \Log::info('工单状态更新成功');
            } catch (\Exception $e) {
                \Log::error('工单状态更新失败:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        return $form;
    }

    /**
     * 处理工单回复提交
     */
    public function handleReply(Request $request, $id)
    {
        // 添加调试日志
        \Log::info('工单回复请求开始:', [
            'id' => $id,
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all(),
            'input' => $request->all()
        ]);
        
        // 测试响应 - 临时调试用
        if ($request->input('test') === '1') {
            return response()->json([
                'status' => true,
                'message' => '测试响应成功',
                'test_data' => $request->all()
            ])->header('Content-Type', 'application/json');
        }
        
        try {
            $adminReply = $request->input('admin_reply');
            $status = $request->input('status');
            $adminReplyTime = $request->input('admin_reply_time');

            // 验证输入
            if (empty($adminReply)) {
                return response()->json([
                    'status' => false,
                    'message' => '回复内容不能为空'
                ], 400);
            }

            // 获取工单信息
            $workOrder = \App\Models\WorkOrder::find($id);
            
            // 如果工单不存在或被删除，返回错误信息
            if (!$workOrder) {
                return response()->json([
                    'status' => false,
                    'message' => '工单不存在或已被删除'
                ], 404);
            }
            
            // 获取当前登录的管理员信息
            $adminId = \Admin::user()->id ?? 0;
            
            // 保存回复记录
            if ($adminReply) {
                try {
                    $reply = \App\Models\WorkOrderReply::create([
                        'work_order_id' => $workOrder->id,
                        'user_id' => null, // 管理员回复，user_id为null
                        'admin_id' => $adminId, // 使用当前登录的管理员ID
                        'content' => $adminReply,
                        'type' => 'admin'
                    ]);
                    
                    \Log::info('工单回复记录创建成功:', $reply->toArray());
                } catch (\Exception $e) {
                    \Log::error('工单回复记录创建失败:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            try {
                // 更新工单状态
                $workOrder->update([
                    'status' => $status,
                    'admin_reply' => $adminReply,
                    'admin_reply_time' => $adminReplyTime ?: now()
                ]);
                
                \Log::info('工单状态更新成功');
            } catch (\Exception $e) {
                \Log::error('工单状态更新失败:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // 记录成功日志
            \Log::info('工单回复处理成功:', [
                'id' => $id,
                'admin_reply' => $adminReply,
                'status' => $status
            ]);
            
            // 返回JSON成功响应
            return response()->json([
                'status' => true,
                'message' => '工单回复成功！'
            ])->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            // 记录错误日志
            \Log::error('工单回复处理失败:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // 返回JSON错误响应
            return response()->json([
                'status' => false,
                'message' => '工单回复失败: ' . $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }
    
    /**
     * 测试工单回复功能
     */
    public function testReply($id)
    {
        return response()->json([
            'status' => true,
            'message' => '测试响应成功',
            'id' => $id,
            'timestamp' => now()->toISOString()
        ])->header('Content-Type', 'application/json');
    }

    /**
     * 更新工单（处理回复）
     */
    public function update($id)
    {
        try {
            $adminReply = request('admin_reply');
            $status = request('status');
            $adminReplyTime = request('admin_reply_time');

            // 使用模型更新工单状态，强制加载用户关联
            $workOrder = \App\Models\WorkOrder::with('user')->find($id);
            
            // 如果工单不存在或被删除，重定向到工单列表页面
            if (!$workOrder) {
                admin_toastr('工单不存在或已被删除', 'error');
                return redirect(admin_url('work-orders'));
            }
            $workOrder->update([
                'status' => $status,
                'admin_reply' => $adminReply,
                'admin_reply_time' => $adminReplyTime
            ]);

            // 如果有回复内容，保存到回复表
            if ($adminReply) {
                // 获取当前登录的管理员信息
                $adminId = \Admin::user()->id ?? 0;
                
                \App\Models\WorkOrderReply::create([
                    'work_order_id' => $id,
                    'user_id' => null, // 管理员回复，user_id为null
                    'admin_id' => $adminId, // 使用当前登录的管理员ID
                    'content' => $adminReply,
                    'type' => 'admin'
                ]);
            }

            // 显示成功消息并重定向
            admin_toastr('工单回复成功！', 'success');
            return redirect()->to(admin_url('work-orders/' . $id));
            
        } catch (\Exception $e) {
            // 显示错误消息并重定向
            admin_toastr('工单更新失败: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * 关闭工单
     */
    public function close($id)
    {
        try {
            // 记录调试信息
            \Log::info('开始关闭工单:', ['id' => $id]);
            
            // 查找工单
            $workOrder = \App\Models\WorkOrder::find($id);
            
            // 如果工单不存在或被删除，重定向到工单列表页面
            if (!$workOrder) {
                admin_toastr('工单不存在或已被删除', 'error');
                return redirect(admin_url('work-orders'));
            }
            
            // 检查工单当前状态
            \Log::info('工单当前状态:', [
                'id' => $workOrder->id,
                'status' => $workOrder->status,
                'closed_at' => $workOrder->closed_at
            ]);
            
            // 检查数据库字段是否存在
            try {
                $tableColumns = \Schema::getColumnListing('work_orders');
                \Log::info('work_orders表字段:', $tableColumns);
                
                if (!in_array('closed_at', $tableColumns)) {
                    throw new \Exception('数据库缺少closed_at字段');
                }
            } catch (\Exception $schemaError) {
                \Log::error('数据库结构检查失败:', [
                    'error' => $schemaError->getMessage(),
                    'trace' => $schemaError->getTraceAsString()
                ]);
                throw new \Exception('数据库结构检查失败: ' . $schemaError->getMessage());
            }
            
            // 更新工单状态
            $updateData = [
                'status' => 'closed',
                'closed_at' => now()
            ];
            
            \Log::info('准备更新的数据:', $updateData);
            
            $result = $workOrder->update($updateData);
            \Log::info('更新结果:', ['result' => $result]);
            
            // 显示成功消息并重定向
            admin_toastr('工单关闭成功！', 'success');
            return redirect()->to(admin_url('work-orders/' . $id));
            
        } catch (\Exception $e) {
            // 记录详细错误信息
            \Log::error('工单关闭失败:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // 显示错误消息并重定向
            admin_toastr('工单关闭失败: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * 重新开启工单
     */
    public function open($id)
    {
        try {
            // 记录调试信息
            \Log::info('开始重新开启工单:', ['id' => $id]);
            
            // 查找工单
            $workOrder = \App\Models\WorkOrder::find($id);
            
            // 如果工单不存在或被删除，重定向到工单列表页面
            if (!$workOrder) {
                admin_toastr('工单不存在或已被删除', 'error');
                return redirect(admin_url('work-orders'));
            }
            
            // 检查工单当前状态
            \Log::info('工单当前状态:', [
                'id' => $workOrder->id,
                'status' => $workOrder->status,
                'closed_at' => $workOrder->closed_at
            ]);
            
            // 检查数据库字段是否存在
            try {
                $tableColumns = \Schema::getColumnListing('work_orders');
                \Log::info('work_orders表字段:', $tableColumns);
                
                if (!in_array('closed_at', $tableColumns)) {
                    throw new \Exception('数据库缺少closed_at字段');
                }
            } catch (\Exception $schemaError) {
                \Log::error('数据库结构检查失败:', [
                    'error' => $schemaError->getMessage(),
                    'trace' => $schemaError->getTraceAsString()
                ]);
                throw new \Exception('数据库结构检查失败: ' . $schemaError->getMessage());
            }
            
            // 更新工单状态
            $updateData = [
                'status' => 'pending',
                'closed_at' => null
            ];
            
            \Log::info('准备更新的数据:', $updateData);
            
            $result = $workOrder->update($updateData);
            \Log::info('更新结果:', ['result' => $result]);
            
            // 显示成功消息并重定向
            admin_toastr('工单重新开启成功！', 'success');
            return redirect()->to(admin_url('work-orders/' . $id));
            
        } catch (\Exception $e) {
            // 记录详细错误信息
            \Log::error('工单重新开启失败:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // 显示错误消息并重定向
            admin_toastr('工单重新开启失败: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * 批量删除工单
     */
    public function batchDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'status' => false,
                    'message' => '请选择要删除的工单'
                ]);
            }
            
            // 直接删除相关回复记录
            \App\Models\WorkOrderReply::whereIn('work_order_id', $ids)->forceDelete();
            
            // 直接删除工单
            $deletedCount = \App\Models\WorkOrder::whereIn('id', $ids)->forceDelete();
            
            return response()->json([
                'status' => true,
                'message' => "成功删除 {$deletedCount} 个工单"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('批量删除工单失败:', [
                'ids' => $ids ?? [],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => '批量删除失败: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 删除工单
     */
    public function destroy($id)
    {
        try {
            // 检查是否是批量删除请求（ID包含逗号）
            if (strpos($id, ',') !== false) {
                $ids = explode(',', $id);
                $ids = array_map('intval', $ids);
                
                \Log::info('批量删除工单请求:', ['ids' => $ids]);
                
                // 批量软删除相关的回复记录
                \App\Models\WorkOrderReply::whereIn('work_order_id', $ids)->delete();
                
                // 批量软删除工单
                $deletedCount = \App\Models\WorkOrder::whereIn('id', $ids)->delete();
                
                return response()->json([
                    'status' => true,
                    'message' => "成功删除 {$deletedCount} 个工单"
                ]);
            }
            
            // 单个工单删除
            $workOrder = \App\Models\WorkOrder::find($id);
            
            // 如果工单不存在或被删除，返回错误响应
            if (!$workOrder) {
                return response()->json([
                    'status' => false,
                    'message' => '工单不存在或已被删除'
                ], 404);
            }
            
            // 直接删除相关的回复记录
            \App\Models\WorkOrderReply::where('work_order_id', $id)->forceDelete();
            
            // 直接删除工单
            $workOrder->forceDelete();
            
            // 返回成功响应
            return response()->json([
                'status' => true,
                'message' => '工单删除成功！'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('工单删除失败:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // 返回错误响应
            return response()->json([
                'status' => false,
                'message' => '工单删除失败: ' . $e->getMessage()
            ], 500);
        }
    }
}
