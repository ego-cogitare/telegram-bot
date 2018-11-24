<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Longman\TelegramBot\Telegram;

/**
 * Class TelegramServiceProvider
 * @package App\Providers
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Telegram::class, function ($app) {
            return new Telegram(env('TELEGRAM_BOT_TOKEN'), env('TELEGRAM_BOT_NAME'));
        });
    }
}