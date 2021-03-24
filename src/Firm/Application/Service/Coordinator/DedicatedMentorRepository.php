<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;

interface DedicatedMentorRepository
{

    public function ofId(string $dedicatedMentorId): DedicatedMentor;
}
