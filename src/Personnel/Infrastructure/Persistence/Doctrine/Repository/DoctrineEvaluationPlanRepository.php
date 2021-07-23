<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Personnel\Domain\Task\Dependency\Firm\Program\EvaluationPlanRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineEvaluationPlanRepository extends DoctrineEntityRepository implements EvaluationPlanRepository
{
    
    public function ofId(string $id): EvaluationPlan
    {
        return $this->findOneByIdOrDie($id, 'evaluation plan');
    }

}
