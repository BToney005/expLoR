<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Deck;
use App\Observers\DeckObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Deck::observe(DeckObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
