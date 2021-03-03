<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

interface ObjectiveProgressReportRepository
{

    public function ofId(string $objectiveProgressReportId): ObjectiveProgressReport;
}
