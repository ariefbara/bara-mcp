<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineObjectiveProgressReportRepository extends DoctrineEntityRepository implements ObjectiveProgressReportRepository
{
    
    public function add(ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->persist($objectiveProgressReport);
    }

    public function ofId(string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->findOneByIdOrDie($objectiveProgressReportId, 'objective progress report');
    }

}
