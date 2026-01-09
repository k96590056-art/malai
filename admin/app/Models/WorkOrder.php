<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 工单模型
 */
class WorkOrder extends Model
{
    use SoftDeletes;
    
    protected $table = 'work_orders';
    
    protected $fillable = [
        'order_no',
        'user_id',
        'username',
        'title',
        'content',
        'category',
        'priority',
        'status',
        'admin_reply',
        'admin_id',
        'admin_reply_time',
        'closed_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'admin_reply_time' => 'datetime',
        'closed_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    /**
     * 获取分类文本
     */
    public function getCategoryTextAttribute()
    {
        $map = [
            'general' => '一般问题',
            'technical' => '技术问题',
            'account' => '账户问题',
            'payment' => '支付问题',
            'game' => '游戏问题',
            'other' => '其他'
        ];
        return $map[$this->category] ?? $this->category;
    }
    
    /**
     * 获取优先级文本
     */
    public function getPriorityTextAttribute()
    {
        $map = [
            'low' => '低',
            'normal' => '普通',
            'high' => '高',
            'urgent' => '紧急'
        ];
        return $map[$this->priority] ?? $this->priority;
    }
    
    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $map = [
            'pending' => '待处理',
            'processing' => '处理中',
            'replied' => '已回复',
            'closed' => '已关闭'
        ];
        return $map[$this->status] ?? $this->status;
    }
    
    /**
     * 工单回复关联
     */
    public function replies()
    {
        return $this->hasMany(WorkOrderReply::class, 'work_order_id');
    }
    
    /**
     * 用户关联
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    /**
     * 获取用户账号
     */
    public function getUserAccountAttribute()
    {
        // 调试信息
        \Log::info('getUserAccountAttribute called', [
            'user_id' => $this->user_id,
            'has_user_relation' => $this->relationLoaded('user'),
            'user_object' => $this->user ? $this->user->toArray() : null,
            'fallback_username' => $this->username
        ]);
        
        if ($this->user && $this->user->username) {
            return $this->user->username;
        }
        return $this->username ?? '未知用户';
    }
}
