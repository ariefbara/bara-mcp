<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Task\PaginationPayload;

interface ProgramRepository
{

    public function allAvailableProgramsForClient(string $clientId, PaginationPayload $paginationPayload): array;

    public function allProgramsSummaryCoordinatedByPersonnel(string $personnelId);

    public function listOfCoordinatedProgramByPersonnel(string $personnelId);

    public function listOfProgram(ProgramListFilter $filter);

    public function aPublishedProgram(string $id): Program;
}
