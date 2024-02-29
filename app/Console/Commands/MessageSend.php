<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Class MessageSend
 * @package App\Console\Commands
 */
class MessageSend extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'message:send';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Send test message';


    /**
     * @param Telegram $telegram
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function handle(Telegram $telegram)
    {
        $result = Request::sendMessage([
            'chat_id' => env('TELEGRAM_BOT_CHAT_ID'),
            'text'    => 'Hello',
        ]);

        if ($result->isOk()) {
            echo 'Message sent successfully';
        } else {
            echo 'Fail: ', $result->getDescription();
        }
    }
}
