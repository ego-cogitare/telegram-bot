<?php

namespace App\Services\Interfaces;


/**
 * Interface IMarketsApi
 */
interface IMarketsApi {
    /**
     * @param array $payload
     * @throws \Exception
     * @return string|json
     */
    public function call(array $payload = []);
}