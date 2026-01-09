<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetDefaultTransferstatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 修改数据库默认值，新注册用户默认不开启自动免转
        DB::statement("ALTER TABLE users MODIFY COLUMN transferstatus int(1) DEFAULT 0 COMMENT '0 转账 1免转'");
        
        // 更新现有用户，将transferstatus为1的用户设置为0（不开启自动免转）
        DB::table('users')
            ->where('transferstatus', 1)
            ->update(['transferstatus' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 恢复默认值
        DB::statement("ALTER TABLE users MODIFY COLUMN transferstatus int(1) DEFAULT 0 COMMENT '0 转账 1免转'");
    }
}

