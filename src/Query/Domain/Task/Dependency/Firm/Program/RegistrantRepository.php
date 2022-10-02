<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Task\Dependency\PaginationFilter;

interface RegistrantRepository
{

    public function allNewRegistrantManageableByPersonnel(string $personnelId, PaginationFilter $pagination);

    public function aRegistrantInProgram(string $programId, string $id): Registrant;
}
