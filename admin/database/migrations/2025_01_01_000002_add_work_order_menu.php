<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddWorkOrderMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 查找或创建"客服管理"父菜单
        $parentMenu = DB::table('admin_menu')->where('title', '客服管理')->first();
        
        if (!$parentMenu) {
            // 创建客服管理父菜单
            $parentId = DB::table('admin_menu')->insertGetId([
                'parent_id' => 0,
                'order' => 60, // 在内容管理之后
                'title' => '客服管理',
                'icon' => 'fa-comments',
                'uri' => '',
                'extension' => '',
                'show' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $parentId = $parentMenu->id;
        }

        // 添加工单管理菜单
        DB::table('admin_menu')->insert([
            'parent_id' => $parentId,
            'order' => 10,
            'title' => '工单管理',
            'icon' => 'fa-ticket',
            'uri' => 'work-orders',
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
        // 删除工单管理菜单
        DB::table('admin_menu')->where('title', '工单管理')->delete();
        
        // 检查客服管理下是否还有其他子菜单，如果没有则删除父菜单
        $parentMenu = DB::table('admin_menu')->where('title', '客服管理')->first();
        if ($parentMenu) {
            $childCount = DB::table('admin_menu')->where('parent_id', $parentMenu->id)->count();
            if ($childCount == 0) {
                DB::table('admin_menu')->where('id', $parentMenu->id)->delete();
            }
        }
    }
}
