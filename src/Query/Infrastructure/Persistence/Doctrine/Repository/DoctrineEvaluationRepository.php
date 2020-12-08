<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\EvaluationRepository,
    Domain\Model\Firm\Program\Participant\Evaluation
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineEvaluationRepository extends EntityRepository implements EvaluationRepository
{

    public function allEvaluationsOfParticipant(
            string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];
        
        $qb = $this->createQueryBuilder("evaluation");
        $qb->select("evaluation")
                ->leftJoin("evaluation.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anEvaluationInProgram(string $firmId, string $programId, string $evaluationId): Evaluation
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "evaluationId" => $evaluationId,
        ];
        
        $qb = $this->createQueryBuilder("evaluation");
        $qb->select("evaluation")
                ->andWhere($qb->expr()->eq("evaluation.id", ":evaluationId"))
                ->leftJoin("evaluation.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: evaluation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
