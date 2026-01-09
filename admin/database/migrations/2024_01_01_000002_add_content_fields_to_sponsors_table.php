<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentFieldsToSponsorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponsors', function (Blueprint $table) {
            // 文章内容字段
            $table->longText('content')->nullable()->comment('文章内容');
            $table->enum('content_type', ['link', 'article'])->default('link')->comment('内容类型：link=链接地址，article=文章内容');
            $table->boolean('is_published')->default(false)->comment('是否发布');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            
            // 添加索引
            $table->index(['content_type', 'is_published']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropIndex(['content_type', 'is_published']);
            $table->dropIndex(['published_at']);
            
            $table->dropColumn(['content', 'content_type', 'is_published', 'published_at']);
        });
    }
}
