<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\LearningProgress;

interface LearningProgressRepository
{

    public function ofId(string $id): LearningProgress;
}
