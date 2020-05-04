<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\Registrant;

interface ProgramRegistrationRepository
{

    public function aProgramRegistrationOfClient(string $clientId, string $programRegistrationId): Registrant;

    public function allProgramRegistrationsOfClient(string $clientId, int $page, int $pageSize);
}
