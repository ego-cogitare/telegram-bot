<?php

namespace App\Services;


use App\Services\Interfaces\IMarketsApi;

/**
 * Class MarketsApi
 * @package App\Services
 */
class MarketsApi implements IMarketsApi
{
    /**
     * @var string
     */
    protected $apiUrl = '';


    /**
     * MarketsApi constructor.
     * @param string $apiProto
     * @param string $apiHost
     * @param int $apiPort
     */
    public function __construct(string $apiProto = 'http', string $apiHost = '', int $apiPort = 80)
    {
        $this->apiUrl = sprintf('%s://%s:%d', $apiProto, $apiHost, $apiPort);
    }


    /**
     * @param array $payload
     * @throws \Exception
     * @return string|json
     */
    public function call(array $payload = [])
    {
        if (!isset($payload[0])) {
            throw new \Exception('Command can not be empty.');
        }

        /** @var string|json $result */
        $result = file_get_contents(sprintf('%s/api/%s', $this->apiUrl, implode('/', $payload)));

        return $result;
    }
}
