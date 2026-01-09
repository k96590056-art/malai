<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * 工单系统API控制器
 */
class WorkOrderController extends Controller
{
    /**
     * 获取工单列表
     */
    public function list(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'nullable|string|in:pending,processing,replied,closed',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => '参数验证失败',
                    'errors' => $validator->errors()
                ]);
            }

            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => '未登录或认证已失效'
                ], 401);
            }

            $status = $request->input('status', '');
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // 构建查询
            $query = DB::table('work_orders');
            
            // 过滤已删除的工单（软删除）
            $query->whereNull('deleted_at');
            // 仅查询当前用户的工单
            $query->where('user_id', $user->id);
            
            // 状态筛选
            if (!empty($status)) {
                $query->where('status', $status);
            }

            // 获取总数
            $total = $query->count();

            // 分页查询
            $list = $query->orderBy('created_at', 'desc')
                         ->offset(($page - 1) * $limit)
                         ->limit($limit)
                         ->get();

            // 处理数据格式
            $list = $list->map(function ($item) {
                return [
                    'id' => $item->id,
                    'order_no' => $item->order_no,
                    'username' => $item->username,
                    'title' => $item->title,
                    'content' => $item->content,
                    'category' => $item->category,
                    'category_text' => $this->getCategoryText($item->category),
                    'priority' => $item->priority,
                    'priority_text' => $this->getPriorityText($item->priority),
                    'status' => $item->status,
                    'status_text' => $this->getStatusText($item->status),
                    'created_at' => $item->created_at,
                    'admin_reply' => $item->admin_reply,
                    'admin_reply_time' => $item->admin_reply_time
                ];
            });

            return response()->json([
                'code' => 200,
                'message' => '获取成功',
                'data' => [
                    'list' => $list,
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 创建工单
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:200',
                'content' => 'required|string|max:2000',
                'category' => 'required|string|in:general,technical,account,payment,game,other',
                'priority' => 'required|string|in:low,normal,high,urgent'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => '参数验证失败',
                    'errors' => $validator->errors()
                ]);
            }

            // 生成工单编号
            $orderNo = 'WO' . date('YmdHis') . rand(1000, 9999);

            // 获取当前用户信息
            $userId = $request->input('user_id', 0);
            $username = $request->input('username', '');
            
            // 如果没有提供用户信息，尝试从认证中获取
            if ($userId == 0 && auth()->check()) {
                $userId = auth()->id();
                $username = auth()->user()->username ?? '用户' . $userId;
            } elseif (empty($username)) {
                // 如果仍然没有用户名，尝试从token或其他方式获取
                $username = $request->input('username', '用户' . ($userId ?: time()));
            }
            
            // 插入工单数据
            $id = DB::table('work_orders')->insertGetId([
                'order_no' => $orderNo,
                'user_id' => $userId,
                'username' => $username,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'category' => $request->input('category'),
                'priority' => $request->input('priority'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'code' => 200,
                'message' => '工单创建成功',
                'data' => [
                    'id' => $id,
                    'order_no' => $orderNo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 获取工单详情
     */
    public function detail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => '参数验证失败',
                    'errors' => $validator->errors()
                ]);
            }

            $id = $request->input('id');

            // 查询工单详情（过滤已删除的工单）
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => '未登录或认证已失效'
                ], 401);
            }

            $workOrder = DB::table('work_orders')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->first();

            if (!$workOrder) {
                return response()->json([
                    'code' => 404,
                    'message' => '工单不存在'
                ]);
            }

            // 查询回复记录（过滤已删除的回复）
            $replies = DB::table('work_order_replies')
                        ->where('work_order_id', $id)
                        ->whereNull('deleted_at')
                        ->orderBy('created_at', 'asc')
                        ->get();

            $data = [
                'id' => $workOrder->id,
                'order_no' => $workOrder->order_no,
                'username' => $workOrder->username,
                'title' => $workOrder->title,
                'content' => $workOrder->content,
                'category' => $workOrder->category,
                'category_text' => $this->getCategoryText($workOrder->category),
                'priority' => $workOrder->priority,
                'priority_text' => $this->getPriorityText($workOrder->priority),
                'status' => $workOrder->status,
                'status_text' => $this->getStatusText($workOrder->status),
                'created_at' => $workOrder->created_at,
                'admin_reply' => $workOrder->admin_reply,
                'admin_reply_time' => $workOrder->admin_reply_time,
                'replies' => $replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'type' => $reply->type,
                        'type_text' => $reply->type === 'admin' ? '客服回复' : '用户回复',
                        'content' => $reply->content,
                        'created_at' => $reply->created_at
                    ];
                })
            ];

            return response()->json([
                'code' => 200,
                'message' => '获取成功',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 回复工单
     */
    public function reply(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'work_order_id' => 'required|integer|min:1',
                'content' => 'required|string|max:2000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => '参数验证失败',
                    'errors' => $validator->errors()
                ]);
            }

            $workOrderId = $request->input('work_order_id');
            $content = $request->input('content');

            // 检查工单是否存在（过滤已删除的工单）
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => '未登录或认证已失效'
                ], 401);
            }

            $workOrder = DB::table('work_orders')
                ->where('id', $workOrderId)
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->first();
            if (!$workOrder) {
                return response()->json([
                    'code' => 404,
                    'message' => '工单不存在或已被删除'
                ]);
            }

            // 获取当前用户信息
            $userId = $request->input('user_id', 0);
            $username = $request->input('username', '');
            
            // 如果没有提供用户信息，尝试从认证中获取
            if ($userId == 0 && auth()->check()) {
                $userId = auth()->id();
                $username = auth()->user()->username ?? '用户' . $userId;
            } elseif (empty($username)) {
                // 如果仍然没有用户名，生成一个
                $username = '用户' . ($userId ?: time());
            }
            
            // 插入回复记录
            $replyId = DB::table('work_order_replies')->insertGetId([
                'work_order_id' => $workOrderId,
                'user_id' => $userId,
                'type' => 'user', // 用户回复
                'content' => $content,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 更新工单状态
            DB::table('work_orders')
                ->where('id', $workOrderId)
                ->update([
                    'status' => 'processing',
                    'updated_at' => now()
                ]);

            return response()->json([
                'code' => 200,
                'message' => '回复成功',
                'data' => [
                    'id' => $replyId
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 关闭工单
     */
    public function close(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => '参数验证失败',
                    'errors' => $validator->errors()
                ]);
            }

            $id = $request->input('id');

            // 检查工单是否存在（过滤已删除的工单）
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => '未登录或认证已失效'
                ], 401);
            }

            $workOrder = DB::table('work_orders')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->first();
            if (!$workOrder) {
                return response()->json([
                    'code' => 404,
                    'message' => '工单不存在或已被删除'
                ]);
            }

            // 更新工单状态为已关闭
            DB::table('work_orders')
                ->where('id', $id)
                ->update([
                    'status' => 'closed',
                    'updated_at' => now()
                ]);

            return response()->json([
                'code' => 200,
                'message' => '工单关闭成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 获取分类文本
     */
    private function getCategoryText($category)
    {
        $map = [
            'general' => '一般问题',
            'technical' => '技术问题',
            'account' => '账户问题',
            'payment' => '支付问题',
            'game' => '游戏问题',
            'other' => '其他'
        ];
        return $map[$category] ?? $category;
    }

    /**
     * 获取优先级文本
     */
    private function getPriorityText($priority)
    {
        $map = [
            'low' => '低',
            'normal' => '普通',
            'high' => '高',
            'urgent' => '紧急'
        ];
        return $map[$priority] ?? $priority;
    }

    /**
     * 获取状态文本
     */
    private function getStatusText($status)
    {
        $map = [
            'pending' => '待处理',
            'processing' => '处理中',
            'replied' => '已回复',
            'closed' => '已关闭'
        ];
        return $map[$status] ?? $status;
    }

    /**
     * 获取当前认证用户
     */
    private function getAuthenticatedUser(Request $request)
    {
        if ($request->has('current_user')) {
            return $request->get('current_user');
        }

        $token = $request->header('Authorization');
        if (empty($token)) {
            return null;
        }

        $token = str_replace('Bearer ', '', $token);
        if (empty($token) || strlen($token) !== 60) {
            return null;
        }

        return User::where('api_token', $token)->first();
    }
}
