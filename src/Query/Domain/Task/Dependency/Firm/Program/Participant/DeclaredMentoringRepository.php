<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;

interface DeclaredMentoringRepository
{

    public function aDeclaredMentoringBelongsToPersonnel(string $personnelId, string $id): DeclaredMentoring;
}
