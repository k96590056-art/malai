<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRegionMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 获取代理管理菜单ID（parent_id = 34）
        // 获取当前最大的菜单ID，确保新菜单ID不冲突
        $maxId = DB::table('admin_menu')->max('id');
        $newMenuId = $maxId + 1;
        
        // 插入地区管理菜单到代理管理下（代理接口的order是28，地区管理使用29）
        DB::table('admin_menu')->insert([
            'id' => $newMenuId,
            'parent_id' => 34, // 代理管理菜单ID
            'order' => 29,
            'title' => '地区管理',
            'icon' => 'fa-map-marker',
            'uri' => 'regions',
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
        // 删除地区管理菜单
        DB::table('admin_menu')->where('uri', 'regions')->where('parent_id', 34)->delete();
    }
}

