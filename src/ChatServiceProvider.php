<?php

namespace Techsmart\Chat;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . "/routes/web.php");
        $this->loadMigrationsFrom(__DIR__ . '/db/migrations');
        $this->loadViewsFrom(__DIR__ . "/resources/views", "chat");

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/techsmart/chat'),
        ]);
        $this->publishes([
            __DIR__.'/db/migrations' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__ . '/../resources/assets' =>
            resource_path('assets/vendor/techsmart/chat'
        )], 'vue-components');
    }
}
