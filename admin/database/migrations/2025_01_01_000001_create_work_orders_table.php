<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 50)->unique()->comment('工单编号');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('username', 50)->comment('用户名');
            $table->string('title', 200)->comment('工单标题');
            $table->text('content')->comment('工单内容');
            $table->string('category', 50)->default('general')->comment('工单分类');
            $table->string('priority', 20)->default('normal')->comment('优先级');
            $table->string('status', 20)->default('pending')->comment('工单状态');
            $table->string('admin_reply', 1000)->nullable()->comment('客服回复');
            $table->unsignedBigInteger('admin_id')->nullable()->comment('处理管理员ID');
            $table->timestamp('admin_reply_time')->nullable()->comment('客服回复时间');
            $table->timestamp('closed_at')->nullable()->comment('关闭时间');
            $table->timestamps();
            
            // 索引
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('order_no');
        });

        // 创建工单回复表
        Schema::create('work_order_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id')->comment('工单ID');
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->unsignedBigInteger('admin_id')->nullable()->comment('管理员ID');
            $table->text('content')->comment('回复内容');
            $table->string('type', 20)->default('user')->comment('回复类型：user/admin');
            $table->timestamps();
            
            // 索引
            $table->index('work_order_id');
            $table->index(['work_order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_order_replies');
        Schema::dropIfExists('work_orders');
    }
}
