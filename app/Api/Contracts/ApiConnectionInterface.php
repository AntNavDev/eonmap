<?php

declare(strict_types=1);

namespace App\Api\Contracts;

interface ApiConnectionInterface
{
    /**
     * Make a GET request to the given endpoint.
     *
     * @param  array<string, mixed>  $params
     * @return array<mixed>
     */
    public function get(string $endpoint, array $params = []): array;
}
