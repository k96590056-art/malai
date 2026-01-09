<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserVip extends Model
{
    
    protected $table = 'user_vip';
    // 允许所有字段批量赋值（包含 *_switch 开关字段）
    protected $guarded = [];
    
}
