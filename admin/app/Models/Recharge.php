<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $table = "recharge";

    protected $guarded = [];
    
    /**
     * 可填充字段
     */
    protected $fillable = [
        'order_no',
        'out_trade_no', 
        'user_id',
        'amount',
        'cash_fee',
        'real_money',
        'pay_way',
        'bank',
        'bank_no',
        'bank_address',
        'bank_owner',
        'info',
        'usdt_rate',
        'state',
        // TRON USDT相关字段
        'tron_tx_hash',
        'tron_usdt_amount',
        'tron_address',
        'tron_network',
        'tron_confirmations',
        'tron_paid_at',
        'tron_verify_result'
    ];

    public function user_data() 
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
