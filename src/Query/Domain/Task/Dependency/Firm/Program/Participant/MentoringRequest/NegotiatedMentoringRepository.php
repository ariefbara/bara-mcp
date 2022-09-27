<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;

interface NegotiatedMentoringRepository
{

    public function aNegotiatedMentoringBelongsToParticipant(string $participantId, string $id): NegotiatedMentoring;

    public function aNegotiatedMentoringBelongsToPersonnel(string $personnelId, string $id): NegotiatedMentoring;

    public function aNegotiatedMentoringInProgram(string $programId, string $id): NegotiatedMentoring;
}
