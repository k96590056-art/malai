<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 工单回复模型
 */
class WorkOrderReply extends Model
{
    use SoftDeletes;
    
    protected $table = 'work_order_replies';
    
    protected $fillable = [
        'work_order_id',
        'user_id',
        'admin_id',
        'type',
        'content'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    /**
     * 工单关联
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
    
    /**
     * 获取类型文本
     */
    public function getTypeTextAttribute()
    {
        $map = [
            'user' => '用户回复',
            'admin' => '客服回复'
        ];
        return $map[$this->type] ?? $this->type;
    }
}
