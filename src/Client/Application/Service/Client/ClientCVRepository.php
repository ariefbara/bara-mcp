<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ClientCV;

interface ClientCVRepository
{

    public function aClientCVCorrespondWithCVForm(string $clientId, string $clientCVFormId): ClientCV;

    public function update(): void;
}
