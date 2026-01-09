<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        
        // 添加客服系统配置
        $this->call(CustomerServiceConfigSeeder::class);
        
        // 添加工单系统配置
        $this->call(WorkOrderSeeder::class);
    }
}
