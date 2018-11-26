<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use TelegramBot\TelegramBotManager\BotManager;
use TelegramBot\TelegramBotManager\Exception\InvalidActionException;
use TelegramBot\TelegramBotManager\Exception\InvalidParamsException;
use Longman\TelegramBot\Request;
use App\Services\MarketsApi;
use App\Helpers\TextTable;

/**
 * Class CommandsListener
 * @package App\Console\Commands
 */
class CommandsListener extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commands:listener';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Send test message';

    /**
     * @var TextTable|null
     */
    protected $textTable = null;


    /**
     * @param string $message
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function sendMessage(string $message = '')
    {
        return Request::sendMessage([
            'chat_id' => env('TELEGRAM_BOT_CHAT_ID'),
            'text' => $message,
'parse_mode' => 'HTML',
        ]);
    }

    /**
     * @param MarketsApi $marketsApi
     * @throws \Exception
     */
    public function handle(MarketsApi $marketsApi)
    {
        /** @var TextTable textTable */
        $this->textTable = new TextTable();

        try {
            $bot = new BotManager([
                'api_key' => env('TELEGRAM_BOT_TOKEN'),
                'bot_username' => env('TELEGRAM_BOT_NAME'),
                'limiter' => [
                    'enabled' => true
                ],
                'commands' => [
                    'paths' => [
                        __DIR__ . '/Telegram',
                    ],
                ],
            ]);
            $bot->getTelegram()->enableMySql([
                'host' => env('DB_HOST'),
                'user' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'database' => env('DB_DATABASE'),
            ]);
            $bot->setCustomGetUpdatesCallback(function ($updates) use ($marketsApi) {
                /** @var \Longman\TelegramBot\Entities\ServerResponse $updates */
                foreach ($updates->getResult() as $command) {
                    /** @var \Longman\TelegramBot\Entities\Update $command */
                    $args = preg_split('~\s+~', $command->getMessage()->getText());

                    switch ($args[0]) {
                        case '/help':
                            $message = 'Available commands list:' . PHP_EOL
                                . '/ping - check bot heartbeat' . PHP_EOL
                                . '/help - show this help' . PHP_EOL
                                . '/markets - get markets list' . PHP_EOL
                                . '/balance <market> <symbol> - get balance for specified market and symbol' . PHP_EOL
                                . '/balances <market> - get all balances for specified market' . PHP_EOL
                                . '/orders <active|history/N> - get list of active or last N orders' . PHP_EOL;
                            break;

                        case '/ping':
                            $message = 'pong';
                            break;

                        case '/balance':
                        case '/balances':
                        case '/markets':
                        case '/orders':
                            $message = [];
                            $result = json_decode($marketsApi->call($args), true);
                            foreach ($result['data']['total'] as $symbol => $amount) {
                                if ($amount == 0 && $result['data']['used'][$symbol] == 0) {
                                    continue;
                                }
                                $message[] = [
                                    'symbol' => $symbol,
                                    'total' => sprintf('%.8f', $amount),
                                    'in_orders' => sprintf('%.8f', $result['data']['used'][$symbol]),
                                ];
                            }
                            break;
                        default:
                            $message = sprintf('Unknown command "%s".' . PHP_EOL . 'Please use "/help" command to display possible commands list', $args[0]);
                    }

                    /** Convert response to text table */
                    if (gettype($message) === 'array') {
                        $this->textTable->setRows($message);
                        $message = sprintf('<pre>%s</pre>', $this->textTable->showHeaders(true)
                            . $this->textTable->render(true));
                    }

                    /** Send message to telegram */
                    $this->sendMessage($message);
                }

                return '';
            });

            while (true) {
                $bot->handleGetUpdates();
                sleep(3);
            }
        } catch (TelegramException $e) {
            \Longman\TelegramBot\TelegramLog::error($e);
        } catch (TelegramException $e) {
        } catch (InvalidActionException $e) {
        } catch (InvalidParamsException $e) {
        }
    }
}
