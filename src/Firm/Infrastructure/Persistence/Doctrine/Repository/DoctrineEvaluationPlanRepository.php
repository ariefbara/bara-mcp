<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\ {
    Application\Service\Coordinator\EvaluationPlanRepository as InterfaceFoorCoordinator,
    Application\Service\Manager\EvaluationPlanRepository,
    Domain\Model\Firm\Program\EvaluationPlan
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineEvaluationPlanRepository extends EntityRepository implements EvaluationPlanRepository, InterfaceFoorCoordinator
{

    public function add(EvaluationPlan $evaluationPlan): void
    {
        $em = $this->getEntityManager();
        $em->persist($evaluationPlan);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $evaluationPlanId): EvaluationPlan
    {
        $evaluationPlan = $this->findOneBy(["id" => $evaluationPlanId]);
        if (empty($evaluationPlan)) {
            $errorDetail = "not found: evaluation plan not found";
            throw RegularException::notFound($errorDetail);
        }
        return $evaluationPlan;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
