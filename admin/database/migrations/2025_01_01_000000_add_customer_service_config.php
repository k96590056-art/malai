<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerServiceConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 添加客服系统配置到system_config表
        DB::table('system_config')->insertOrIgnore([
            ['key' => 'gongdan_url', 'value' => ''],
            ['key' => 'service_type', 'value' => 'kefu'],
            ['key' => 'show_selector', 'value' => '0'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 删除添加的配置项
        DB::table('system_config')->whereIn('key', [
            'gongdan_url',
            'service_type',
            'show_selector'
        ])->delete();
    }
}
