<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 添加工单系统相关配置
        $configs = [
            [
                'key' => 'work_order_auto_close_days',
                'value' => '30',
                'description' => '工单自动关闭天数',
            ],
            [
                'key' => 'work_order_notification',
                'value' => '1',
                'description' => '工单通知开关',
            ],
            [
                'key' => 'work_order_priority_default',
                'value' => 'normal',
                'description' => '工单默认优先级',
            ],
            [
                'key' => 'work_order_category_default',
                'value' => 'general',
                'description' => '工单默认分类',
            ],
        ];

        foreach ($configs as $config) {
            DB::table('system_config')->insertOrIgnore([
                'key' => $config['key'],
                'value' => $config['value'],
            ]);
        }

        $this->command->info('工单系统配置已添加');
    }
}
