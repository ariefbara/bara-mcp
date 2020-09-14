<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function ofId(string $firmId, string $programId): Program;

    public function allProgramForUser(int $page, int $pageSize);
}
