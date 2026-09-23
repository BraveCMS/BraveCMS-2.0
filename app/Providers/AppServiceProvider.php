<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Providers\FacebookProvider;

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
    // Database bug fix
    Schema::defaultStringLength(191);

    // Use Twitter Bootstrap pagination
    Paginator::useBootstrap();

    $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');

    $socialite->extend('facebook', function ($app) use ($socialite) {
      $config = $app['config']['services.facebook'];

      return $socialite->buildProvider(FacebookProvider::class, $config);
    });

    Blade::if('userCan', function ($permission) {
      return in_array(
        $permission,
        Auth::user()->role->permissions->pluck('slug')->toArray()
      );
    });
  }
}