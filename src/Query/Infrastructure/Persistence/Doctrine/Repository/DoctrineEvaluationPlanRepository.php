<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\{
    Application\Service\Firm\Program\EvaluationPlanRepository,
    Domain\Model\Firm\Program\EvaluationPlan
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};
use TheSeer\Tokenizer\Exception;

class DoctrineEvaluationPlanRepository extends EntityRepository implements EvaluationPlanRepository
{

    public function allEvaluationPlansInProgram(
            string $firmid, string $programId, int $page, int $pageSize, ?bool $enableOnly = true)
    {
        $params = [
            "firmId" => $firmid,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("evaluationPlan");
        $qb->select("evaluationPlan")
                ->leftJoin("evaluationPlan.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        if ($enableOnly) {
            $qb->andWhere($qb->expr()->eq("evaluationPlan.disabled", "false"));
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anEvaluationPlanInProgram(string $firmid, string $programId, string $evaluationPlanId): EvaluationPlan
    {
        $params = [
            "firmId" => $firmid,
            "programId" => $programId,
            "evaluationPlanId" => $evaluationPlanId,
        ];

        $qb = $this->createQueryBuilder("evaluationPlan");
        $qb->select("evaluationPlan")
                ->andWhere($qb->expr()->eq("evaluationPlan.id", ":evaluationPlanId"))
                ->leftJoin("evaluationPlan.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (Exception $ex) {
            $errorDetail = "not found: evaluation plan not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function anEvaluationPlanInFirm(string $firmid, string $evaluationPlanId): EvaluationPlan
    {
        $params = [
            "firmId" => $firmid,
            "evaluationPlanId" => $evaluationPlanId,
        ];

        $qb = $this->createQueryBuilder("evaluationPlan");
        $qb->select("evaluationPlan")
                ->andWhere($qb->expr()->eq("evaluationPlan.id", ":evaluationPlanId"))
                ->leftJoin("evaluationPlan.program", "program")
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (Exception $ex) {
            $errorDetail = "not found: evaluation plan not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
