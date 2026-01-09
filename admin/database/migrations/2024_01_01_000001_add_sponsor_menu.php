<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSponsorMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 直接插入赞助管理菜单到内容管理下
        DB::table('admin_menu')->insert([
            'id' => 65,
            'parent_id' => 50, // 内容管理菜单ID
            'order' => 40,     // 在文章管理之后
            'title' => '赞助管理',
            'icon' => 'fa-handshake-o',
            'uri' => 'sponsors',
            'extension' => '',
            'show' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 删除赞助管理菜单
        DB::table('admin_menu')->where('id', 65)->delete();
    }
}
