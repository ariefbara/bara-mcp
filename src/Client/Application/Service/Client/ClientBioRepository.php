<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ClientBio;

interface ClientBioRepository
{

    public function aBioCorrespondWithForm(string $clientId, string $bioFormId): ClientBio;

    public function update(): void;
}
