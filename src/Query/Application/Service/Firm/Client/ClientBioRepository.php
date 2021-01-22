<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientBio;

interface ClientBioRepository
{

    public function aBioBelongsToClientCorrespondWithBioForm(
            string $firmId, string $clientId, string $bioFormId): ClientBio;

    public function allBiosBelongsClient(string $firmId, string $clientId, int $page, int $pageSize);
}
