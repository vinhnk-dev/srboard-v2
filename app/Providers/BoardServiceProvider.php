<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BoardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            \App\Repositories\BoardRepository::class
        );

        $this->app->singleton(
            \App\Repositories\BoardCategoryRepository::class
        );

        $this->app->singleton(
            \App\Repositories\BoardCommentRepository::class
        );

        $this->app->singleton(
            \App\Repositories\BoardFileRepository::class
        );

        $this->app->singleton(
            \App\Repositories\BoardTypeRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}