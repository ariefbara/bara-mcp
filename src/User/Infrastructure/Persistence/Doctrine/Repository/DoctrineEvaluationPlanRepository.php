<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;
use User\{
    Application\Service\Personnel\Coordinator\EvaluationPlanRepository,
    Domain\DependencyModel\Firm\Program\EvaluationPlan
};

class DoctrineEvaluationPlanRepository extends EntityRepository implements EvaluationPlanRepository
{

    public function ofId(string $evaluationPlanId): EvaluationPlan
    {
        $evaluationPlan = $this->findOneBy(["id" => $evaluationPlanId]);
        if (empty($evaluationPlan)) {
            $errorDetail = "not found: evaluation plan not found";
            throw RegularException::notFound($errorDetail);
        }
        return $evaluationPlan;
    }

}
