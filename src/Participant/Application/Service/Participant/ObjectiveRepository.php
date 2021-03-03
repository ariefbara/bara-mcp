<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\OKRPeriod\Objective;

interface ObjectiveRepository
{

    public function ofId(string $objectiveId): Objective;
}
