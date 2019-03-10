<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use App\Models\Arbitrage as Model;

/**
 * Class TripletLogJob
 * @package App\Jobs
 */
class TripletLogJob implements ShouldQueue
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
        /** Send message to telegram */
        if ($this->payload['notify']) {
            $result = Request::sendMessage([
                'chat_id' => env('TELEGRAM_BOT_CHAT_ID'),
                'text' => $this->payload['log'],
            ]);

            if ($result->isOk()) {
                echo 'Message sent successfully';
            } else {
                echo 'Sorry message not sent';
            }
        }

        /** Save found triplet information */
        Model::create([
            'triplet' => $this->payload['triplet'],
            'stock_id' => $this->payload['stock_id'],
            'profit' => $this->payload['profit'],
            'profit_quote' => $this->payload['profit_quote'],
            'bet' => $this->payload['bet'],
            'notify' => boolval($this->payload['notify']),
        ]);
    }
}
