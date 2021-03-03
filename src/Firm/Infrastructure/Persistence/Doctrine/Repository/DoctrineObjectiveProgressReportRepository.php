<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Coordinator\ObjectiveProgressReportRepository;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineObjectiveProgressReportRepository extends DoctrineEntityRepository implements ObjectiveProgressReportRepository
{
    
    public function ofId(string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->findOneByIdOrDie($objectiveProgressReportId, 'objective progress report');
    }

}
