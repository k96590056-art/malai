<?php

use Illuminate\Database\Seeder;
use App\Models\Sponsor;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 清空现有数据
        Sponsor::truncate();
        
        // 创建测试数据
        $sponsors = [
            [
                'name' => '尤文图斯',
                'title' => '官方区域合作伙伴',
                'logo' => 'ddf471901f2b4fff9ee57015a1698227.png',
                'banner' => '93b000fa1d3246ce9b90a62c018714af.png',
                'description' => '尤文图斯足球俱乐部是意大利最成功的足球俱乐部之一，拥有悠久的历史和辉煌的成就。',
                'status' => 'active',
                'sort_order' => 1,
                'link_url' => '/zhanzhuye?type=1',
                'link_type' => 'internal',
            ],
            [
                'name' => '阿斯顿维拉',
                'title' => '官方全球顶级合作伙伴',
                'logo' => 'ddf471901f2b4fff9ee57015a1698227.png',
                'banner' => 'bd72c14c428d41ce8105a0d82a1bb696.png',
                'description' => '阿斯顿维拉足球俱乐部是英格兰足球超级联赛的知名俱乐部，拥有丰富的足球传统。',
                'status' => 'active',
                'sort_order' => 2,
                'link_url' => '/zhanzhuye?type=2',
                'link_type' => 'internal',
            ],
        ];
        
        foreach ($sponsors as $sponsor) {
            Sponsor::create($sponsor);
        }
        
        $this->command->info('赞助商数据初始化完成！');
    }
}
