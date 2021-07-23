<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor\EvaluationReport;
use Personnel\Domain\Task\DedicatedMentor\EvaluationReportRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMentorEvaluationReportRepository extends DoctrineEntityRepository implements EvaluationReportRepository
{
    
    public function ofId(string $id): EvaluationReport
    {
        return $this->findOneByIdOrDie($id, 'evaluation report');
    }

}
