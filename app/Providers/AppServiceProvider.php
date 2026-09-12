<?php

namespace App\Providers;

use App\Models\NavigationItem;
use App\Models\SiteSetting;
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
            $siteSettings = collect();
            if (Schema::hasTable('navigation_items')) {
                $navigation = NavigationItem::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
                    ->unique(fn (NavigationItem $item) => mb_strtolower(rtrim($item->url, '/')))
                    ->values();
            }
            if (Schema::hasTable('site_settings')) {
                $siteSettings = SiteSetting::query()->pluck('value', 'key');
            }
            $view->with([
                'navigation' => $navigation,
                'siteSettings' => $siteSettings,
            ]);
        });
    }
}
