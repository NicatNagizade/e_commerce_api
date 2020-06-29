<?php

namespace App\Providers;

use App\Helpers\Mixins\BuilderMixin;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

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
        if ($lang = request()->header('Content-lang')) {
            app()->setLocale($lang);
        }

        Builder::mixin(new BuilderMixin);
    }
}
