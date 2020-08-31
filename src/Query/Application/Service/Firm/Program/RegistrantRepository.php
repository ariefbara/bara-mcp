<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Registrant;

interface RegistrantRepository
{

    public function ofId(string $firmId, string $programId, string $registrantId): Registrant;

    public function all(string $firmId, string $programId, int $page, int $pageSize);
}
