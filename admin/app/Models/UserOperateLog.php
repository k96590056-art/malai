<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOperateLog extends Model
{
    protected $guarded = [];
    
    public function user_data()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public static function insertLog($user_id,$type,$login_ua,$login_ip,$ip_address,$desc='',$info='',$region_id=null,$hand=null)
    {
        self::create([
            'user_id' => $user_id,
            'type' => $type,
            'login_ua' => $login_ua,
            'login_ip' => $login_ip,
            'ip_address' => $ip_address,
            'desc' => $desc,
            'info' => $info,
            'region_id' => $region_id,
            'hand' => $hand,
        ]);
        return true;
    }
}
