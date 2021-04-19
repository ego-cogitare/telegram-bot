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
use Log;

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
        Log::debug('incoming payload', $this->payload);

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

        /**
         * Triplet processing progress:
         *  0 - means first operation was not processed
         *  1 - second operation was not processed but first is
         *  2 - only 3rd operation was no processed
         *  3 - all operations were processed
         */
        $progress = 0;
        foreach ($this->payload['process_log'] as $stepLog) {
            $progress += intval($stepLog['succeed']);
        }

        /** Save found triplet information */
        Model::create([
            'triplet' => $this->payload['triplet'],
            'time_start' => $this->payload['time_start'] ?? time() * 1000,
            'time_delay' => $this->payload['time_delay'] ?? 0,
            'stock_id' => $this->payload['stock_id'],
            'profit' => $this->payload['profit'],
            'profit_quote' => $this->payload['profit_quote'],
            'bet' => $this->payload['bet'],
            'notify' => boolval($this->payload['notify']),
            'progress' => $progress,
            'error' => $this->payload['error'] ?? '',
        ]);
    }
}
