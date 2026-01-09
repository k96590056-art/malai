<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 赞助商模型
 */
class Sponsor extends Model
{
    protected $fillable = [
        'name', 'title', 'logo', 'banner', 'description', 
        'status', 'sort_order', 'link_url', 'link_type',
        'content', 'content_type', 'is_published', 'published_at'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        return $this->status === 'active' ? '正常' : '禁用';
    }

    /**
     * 获取Logo完整URL
     */
    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return '';
        }
        // 如果已经是完整URL，直接返回
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }
        // 如果是相对路径，拼接完整URL
        if (str_starts_with($this->logo, '/')) {
            return env('APP_URL') . $this->logo;
        }
        // 默认情况，拼接uploads目录
        return env('APP_URL') . '/uploads/' . $this->logo;
    }

    /**
     * 获取Banner完整URL
     */
    public function getBannerUrlAttribute()
    {
        if (empty($this->banner)) {
            return '';
        }
        // 如果已经是完整URL，直接返回
        if (str_starts_with($this->banner, 'http')) {
            return $this->banner;
        }
        // 如果是相对路径，拼接完整URL
        if (str_starts_with($this->banner, '/')) {
            return env('APP_URL') . $this->banner;
        }
        // 默认情况，拼接uploads目录
        return env('APP_URL') . '/uploads/' . $this->banner;
    }

    /**
     * 获取内容类型文本
     */
    public function getContentTypeTextAttribute()
    {
        $typeMap = [
            'link' => '链接地址',
            'article' => '文章内容'
        ];
        
        return $typeMap[$this->content_type] ?? '链接地址';
    }

    /**
     * 获取发布状态文本
     */
    public function getPublishedTextAttribute()
    {
        return $this->is_published ? '已发布' : '未发布';
    }

    /**
     * 只获取激活状态的赞助商
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * 只获取已发布的赞助商
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * 按排序字段排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }
}
