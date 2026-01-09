<?php

use Illuminate\Database\Seeder;
use App\Models\SystemConfig;

class CustomerServiceConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 添加客服系统配置
        $configs = [
            'gongdan_url' => '',
            'service_type' => 'kefu',
            'show_selector' => '0',
        ];

        foreach ($configs as $key => $value) {
            SystemConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('客服系统配置已添加完成！');
    }
}
