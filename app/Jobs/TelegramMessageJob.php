<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Class TelegramMessageJob
 * @package App\Jobs
 */
class TelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array|null
     */
    protected $payload = null;

    /**
     * EnqueueJob constructor.
     * @param array $payload
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function handle(Telegram $telegram)
    {
        $result = Request::sendMessage([
            'chat_id' => env('TELEGRAM_BOT_CHAT_ID'),
            'text' => $this->payload['me0ssage'],
        ]);

        if ($result->isOk()) {
            echo 'Message sent successfully';
        } else {
            echo 'Sorry message not sent';
        }
    }
}
