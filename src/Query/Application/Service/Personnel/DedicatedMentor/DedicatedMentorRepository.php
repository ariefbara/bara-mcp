<?php

namespace Query\Application\Service\Personnel\DedicatedMentor;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

interface DedicatedMentorRepository
{

    public function aDedicatedMentorOfPersonnel(string $personnelId, string $dedicatedMentorId): DedicatedMentor;
}
