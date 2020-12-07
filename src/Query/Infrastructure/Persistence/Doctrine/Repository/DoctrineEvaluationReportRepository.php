<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Personnel\ProgramCoordinator\EvaluationReportRepository as InterfaceForCoordinator;
use Query\Application\Service\Firm\Program\Participant\EvaluationReportRepository;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineEvaluationReportRepository extends EntityRepository implements EvaluationReportRepository, InterfaceForCoordinator
{
    public function allEvaluationReportsBelongsToProgramParticipant(string $firmId, string $programId, $participantId,
            int $page, int $pageSize, ?string $evaluationPlanId)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $qb = $this->createQueryBuilder("evaluationReport");
        $qb->select("evaluationReport")
                ->leftJoin("evaluationReport.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        if (isset($evaluationPlanId)) {
            $qb->leftJoin("evaluationReport.evaluationPlan", "evaluationPlan")
                    ->andWhere($qb->expr()->eq("evaluationPlan.id", ":evaluationPlanId"))
                    ->setParameter("evaluationPlanId", $evaluationPlanId);
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anEvaluationReportInProgram(string $firmId, string $programId, string $evaluationReportId): EvaluationReport
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "evaluationReportId" => $evaluationReportId,
        ];

        $qb = $this->createQueryBuilder("evaluationReport");
        $qb->select("evaluationReport")
                ->andWhere($qb->expr()->eq("evaluationReport.id", ":evaluationReportId"))
                ->leftJoin("evaluationReport.evaluationPlan", "evaluationPlan")
                ->leftJoin("evaluationPlan.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: evaluation report not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allEvaluationReportsBelongsToCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorId" => $coordinatorId,
        ];
        
        $qb = $this->createQueryBuilder("evaluationReport");
        $qb->select("evaluationReport")
                ->leftJoin("evaluationReport.coordinator", "coordinator")
                ->andWhere($qb->expr()->eq("coordinator.id", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anEvaluationReportBelongsToPersonnel(
            string $firmId, string $personnelId, string $participantId, string $evaluationPlanId): EvaluationReport
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "participantId" => $participantId,
            "evaluationPlanId" => $evaluationPlanId,
        ];
        
        $qb = $this->createQueryBuilder("evaluationReport");
        $qb->select("evaluationReport")
                ->leftJoin("evaluationReport.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("evaluationReport.evaluationPlan", "evaluationPlan")
                ->andWhere($qb->expr()->eq("evaluationPlan.id", ":evaluationPlanId"))
                ->leftJoin("evaluationReport.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: evaluation report not found";
            throw RegularException::notFound($errorDetail);
        }
    }


}
