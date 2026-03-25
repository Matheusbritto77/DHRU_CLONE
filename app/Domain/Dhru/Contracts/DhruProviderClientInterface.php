<?php

namespace App\Domain\Dhru\Contracts;

use App\Models\DhruProvider;

interface DhruProviderClientInterface
{
    public function fetchServiceCatalog(DhruProvider $provider): array;

    public function request(DhruProvider $provider, string $action, array|string $parameters, bool $rawParameters = false): array;
}
