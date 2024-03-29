<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Task\Dependency\PaginationFilter;

interface MentoringRequestRepository
{

    public function aMentoringRequestBelongsToParticipant(string $participantId, string $id): MentoringRequest;

    public function aMentoringRequestBelongsToPersonnel(string $personnelId, string $id): MentoringRequest;

    public function allMentoringRequestBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringRequestSearch $mentoringRequestSearch);

    public function aMentoringRequestInProgram(string $programId, string $id): MentoringRequest;
    
    public function allUnconcludedMentoringRequestsInProgramManageableByPersonnel(string $personnelId, PaginationFilter $paginationFilter);
}
