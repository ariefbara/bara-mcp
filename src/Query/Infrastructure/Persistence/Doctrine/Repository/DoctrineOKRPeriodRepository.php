<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Coordinator\OKRPeriodRepository as InterfaceForCoordinator;
use Query\Application\Service\Participant\OKRPeriodRepository as OKRPeriodRepository2;
use Query\Application\Service\TeamMember\OKRPeriodRepository;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineOKRPeriodRepository extends DoctrineEntityRepository implements OKRPeriodRepository, InterfaceForCoordinator,
        OKRPeriodRepository2
{

    public function allOKRPeriodsBelongsToParticipant(string $participantId, int $page, int $pageSize)
    {
        $params = [
            'participantId' => $participantId,
        ];
        $qb = $this->createQueryBuilder('okrPeriod');
        $qb->select('okrPeriod')
                ->leftJoin('okrPeriod.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anOKRPeriodBelongsToParticipant(string $participantId, string $okrPeriodId): OKRPeriod
    {
        $params = [
            'participantId' => $participantId,
            'okrPeriodId' => $okrPeriodId,
        ];
        $qb = $this->createQueryBuilder('okrPeriod');
        $qb->select('okrPeriod')
                ->andWhere($qb->expr()->eq('okrPeriod.id', ':okrPeriodId'))
                ->leftJoin('okrPeriod.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: okr period not found');
        }
    }

    public function allOKRPeriodsBelongsToParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            'programId' => $programId,
            'participantId' => $participantId,
        ];
        $qb = $this->createQueryBuilder('okrPeriod');
        $qb->select('okrPeriod')
                ->leftJoin('okrPeriod.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anOKRPeriodInProgram(string $programId, string $okrPeriodId): OKRPeriod
    {
        $params = [
            'programId' => $programId,
            'okrPeriodId' => $okrPeriodId,
        ];
        $qb = $this->createQueryBuilder('okrPeriod');
        $qb->select('okrPeriod')
                ->andWhere($qb->expr()->eq('okrPeriod.id', ':okrPeriodId'))
                ->leftJoin('okrPeriod.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: okr period not found');
        }
    }

}
