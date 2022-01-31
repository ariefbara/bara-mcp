<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Consultant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;

interface MentoringSlotRepository
{

    public function ofId(string $id): MentoringSlot;
}
