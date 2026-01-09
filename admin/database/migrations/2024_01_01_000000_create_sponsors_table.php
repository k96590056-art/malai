<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('赞助名称');
            $table->string('title', 200)->comment('赞助标题');
            $table->string('logo', 255)->nullable()->comment('赞助商Logo');
            $table->string('banner', 255)->nullable()->comment('活动图片');
            $table->text('description')->nullable()->comment('赞助详情');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('状态：active=正常，inactive=禁用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->string('link_url', 255)->nullable()->comment('跳转链接');
            $table->string('link_type', 50)->default('internal')->comment('链接类型：internal=内部链接，external=外部链接');
            $table->timestamps();
            
            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponsors');
    }
}
