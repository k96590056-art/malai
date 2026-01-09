<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // 兼容 Dcat Admin 调用 Str::html 不存在的问题
        if (!\Illuminate\Support\Str::hasMacro('html')) {
            \Illuminate\Support\Str::macro('html', function ($value) {
                return new \Illuminate\Support\HtmlString($value);
            });
        }
    }
}
