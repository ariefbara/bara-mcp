<?php

namespace Client\Application\Service\Client;

use Client\Domain\DependencyModel\Firm\ClientCVForm;

interface ClientCVFormRepository
{
    public function ofId(string $clientCVFormId): ClientCVForm;
}
