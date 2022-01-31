<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest;

use Participant\Domain\Model\Participant\MentoringRequest\NegotiatedMentoring;

interface NegotiatedMentoringRepository
{

    public function ofId(string $id): NegotiatedMentoring;
}
