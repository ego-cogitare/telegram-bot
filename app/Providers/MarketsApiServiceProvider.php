<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Services\MarketsApi;

/**
 * Class MarketsApiServiceProvider
 * @package App\Providers
 */
class MarketsApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MarketsApi::class, function ($app) {
            return new MarketsApi(env('MARKETS_API_PROTO'), env('MARKETS_API_HOST'), env('MARKETS_API_PORT'));
        });
    }
}