<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

interface ObjectiveProgressReportRepository
{

    public function nextIdentity(): string;

    public function add(ObjectiveProgressReport $objectiveProgressReport): void;
    
    public function ofId(string $objectiveProgressReportId): ObjectiveProgressReport;
}
