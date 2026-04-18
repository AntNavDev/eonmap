<?php

declare(strict_types=1);

namespace App\Api;

class PbdbApiConnection extends AbstractApiConnection
{
    public function __construct()
    {
        parent::__construct(config('api.pbdb.base_url'));
    }
}
