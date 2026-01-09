<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTronFieldsToRechargeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recharge', function (Blueprint $table) {
            // TRON USDT充值相关字段
            $table->string('tron_tx_hash', 255)->nullable()->comment('TRON交易哈希');
            $table->decimal('tron_usdt_amount', 15, 6)->nullable()->comment('TRON USDT充值金额');
            $table->string('tron_address', 255)->nullable()->comment('TRON收款地址');
            $table->string('tron_network', 50)->nullable()->comment('TRON网络类型(TRC20)');
            $table->integer('tron_confirmations')->nullable()->default(0)->comment('TRON交易确认数');
            $table->timestamp('tron_paid_at')->nullable()->comment('TRON支付时间');
            $table->text('tron_verify_result')->nullable()->comment('TRON验证结果');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recharge', function (Blueprint $table) {
            $table->dropColumn([
                'tron_tx_hash',
                'tron_usdt_amount', 
                'tron_address',
                'tron_network',
                'tron_confirmations',
                'tron_paid_at',
                'tron_verify_result'
            ]);
        });
    }
}
