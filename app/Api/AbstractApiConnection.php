<?php

declare(strict_types=1);

namespace App\Api;

use App\Api\Contracts\ApiConnectionInterface;
use App\Api\Exceptions\ApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractApiConnection implements ApiConnectionInterface
{
    public function __construct(protected string $baseUrl) {}

    /**
     * Make a GET request to the given endpoint.
     *
     * Appends `.json` if not already present. Throws ApiException on non-2xx
     * responses or connection failures.
     *
     * @param  array<string, mixed>  $params
     * @return array<mixed>
     *
     * @throws ApiException
     */
    public function get(string $endpoint, array $params = []): array
    {
        if (! str_ends_with($endpoint, '.json')) {
            $endpoint .= '.json';
        }

        $url = $this->baseUrl.$endpoint;

        try {
            $response = Http::get($url, $params);
        } catch (ConnectionException $e) {
            Log::channel('api')->error('API connection failed', [
                'url' => $url,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            throw new ApiException('Connection failed: '.$e->getMessage(), 0, $e);
        }

        if (! $response->successful()) {
            Log::channel('api')->error('API request failed', [
                'url' => $url,
                'params' => $params,
                'status' => $response->status(),
                'response' => mb_substr($response->body(), 0, 500),
            ]);

            throw new ApiException(
                'API request failed with status '.$response->status().': '.$response->body(),
                $response->status()
            );
        }

        return $response->json();
    }
}
