<?php

namespace App\Providers;

use App\Models\NavigationItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.public', function ($view) {
            $navigation = collect();
            if (Schema::hasTable('navigation_items')) {
                $navigation = NavigationItem::query()->where('is_active', true)->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
            }
            $view->with('navigation', $navigation);
        });
    }
}
