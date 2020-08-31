<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientRegistrant;

interface ProgramRegistrationRepository
{

    public function ofId(string $firmId, string $clientId, string $programRegistrationId): ClientRegistrant;

    public function all(string $firmId, string $clientId, int $page, int $pageSize);
}
