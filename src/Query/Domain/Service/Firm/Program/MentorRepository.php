<?php

namespace Query\Domain\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Consultant;

interface MentorRepository
{

    public function aMentorInProgram(string $programId, string $mentorId): Consultant;

    public function allMentorsAccessibleToParticipant(string $participantId, int $page, int $pageSize);
}
