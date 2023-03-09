<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Query\Domain\Model\Firm\Program\Registrant;
use Resources\OffsetLimit;
use Resources\SearchFilter;

interface RegistrantRepository
{

    public function allNewRegistrantManageableByPersonnel(
            string $personnelId, SearchFilter $searchFilter, OffsetLimit $offsetLimit);

    public function aRegistrantInProgram(string $programId, string $id): Registrant;
}
