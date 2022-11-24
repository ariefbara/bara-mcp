<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantTask;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantTaskRepository;

class DoctrineConsultantTaskRepository extends EntityRepository implements ConsultantTaskRepository
{
    public function aConsultantTaskDetailForParticipant(string $participantId, string $id): ConsultantTask
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('consultantTask');
        $qb->select('consultantTask')
                ->andWhere($qb->expr()->eq('consultantTask.id', ':id'))
                ->leftJoin('consultantTask.task', 'task')
                ->leftJoin('task.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('consultant task not found');
        }
    }

    public function aConsultantTaskDetailInProgram(string $programId, string $id): ConsultantTask
    {
        $parameters = [
            'programId' => $programId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('consultantTask');
        $qb->select('consultantTask')
                ->andWhere($qb->expr()->eq('consultantTask.id', ':id'))
                ->leftJoin('consultantTask.task', 'task')
                ->leftJoin('task.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('consultant task not found');
        }
    }

}
