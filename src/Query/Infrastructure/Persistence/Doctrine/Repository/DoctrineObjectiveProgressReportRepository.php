<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Coordinator\ObjectiveProgressReportRepository as InterfaceForCoordinator;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Service\ObjectiveProgressReportRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineObjectiveProgressReportRepository extends DoctrineEntityRepository
        implements ObjectiveProgressReportRepository, InterfaceForCoordinator
{
    
    public function allObjectiveProgressReportsInObjectiveBelongsToParticipant(string $participantId,
            string $objectiveId, int $page, int $pageSize)
    {
        $params = [
            'participantId' => $participantId,
            'objectiveId' => $objectiveId,
        ];
        $qb = $this->createQueryBuilder('objectiveProgressReport');
        $qb->select('objectiveProgressReport')
                ->leftJoin('objectiveProgressReport.objective', 'objective')
                ->andWhere($qb->expr()->eq('objective.id', ':objectiveId'))
                ->leftJoin('objective.okrPeriod', 'okrPeriod')
                ->leftJoin('okrPeriod.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anObjectiveProgressReportBelongsToParticipant(string $participantId,
            string $objectiveProgressReportId): ObjectiveProgressReport
    {
        $params = [
            'participantId' => $participantId,
            'objectiveProgressReportId' => $objectiveProgressReportId,
        ];
        $qb = $this->createQueryBuilder('objectiveProgressReport');
        $qb->select('objectiveProgressReport')
                ->andWhere($qb->expr()->eq('objectiveProgressReport.id', ':objectiveProgressReportId'))
                ->leftJoin('objectiveProgressReport.objective', 'objective')
                ->leftJoin('objective.okrPeriod', 'okrPeriod')
                ->leftJoin('okrPeriod.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: objective progress report not found');
        }
    }

    public function allObjectiveProgressReportsBelongsToObjectiveInProgram(string $programId, string $objectiveId,
            int $page, int $pageSize)
    {
        $params = [
            'programId' => $programId,
            'objectiveId' => $objectiveId,
        ];
        $qb = $this->createQueryBuilder('objectiveProgressReport');
        $qb->select('objectiveProgressReport')
                ->leftJoin('objectiveProgressReport.objective', 'objective')
                ->andWhere($qb->expr()->eq('objective.id', ':objectiveId'))
                ->leftJoin('objective.okrPeriod', 'okrPeriod')
                ->leftJoin('okrPeriod.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anObjectiveProgressReportInProgram(string $programId, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        $params = [
            'programId' => $programId,
            'objectiveProgressReportId' => $objectiveProgressReportId,
        ];
        $qb = $this->createQueryBuilder('objectiveProgressReport');
        $qb->select('objectiveProgressReport')
                ->andWhere($qb->expr()->eq('objectiveProgressReport.id', ':objectiveProgressReportId'))
                ->leftJoin('objectiveProgressReport.objective', 'objective')
                ->leftJoin('objective.okrPeriod', 'okrPeriod')
                ->leftJoin('okrPeriod.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: objective progress report not found');
        }
    }

}
