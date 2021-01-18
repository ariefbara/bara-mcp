<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientCV;

interface ClientCVRepository
{

    public function aClientCVBelongsClientCorrespondWithClientCVForm(
            string $firmId, string $clientId, string $clientCVFormId): ClientCV;

    public function allClientCVsBelongsClient(string $firmId, string $clientId, int $page, int $pageSize);
}
