<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Task\PaginationPayload;

interface ProgramRepository
{

    public function allAvailableProgramsForClient(string $clientId, PaginationPayload $paginationPayload): array;
}
