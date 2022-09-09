<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;

interface MentoringRequestRepository
{

    public function aMentoringRequestBelongsToParticipant(string $participantId, string $id): MentoringRequest;

    public function aMentoringRequestBelongsToPersonnel(string $personnelId, string $id): MentoringRequest;

    public function allMentoringRequestBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringRequestSearch $mentoringRequestSearch);
}
