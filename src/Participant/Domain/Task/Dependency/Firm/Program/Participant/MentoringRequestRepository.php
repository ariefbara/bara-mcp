<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\MentoringRequest;

interface MentoringRequestRepository
{

    public function nextIdentity(): string;

    public function add(MentoringRequest $mentoringRequest);

    public function ofId(string $id): MentoringRequest;
}
