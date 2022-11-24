<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorTask;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorTaskRepository;
use Resources\Exception\RegularException;

class DoctrineCoordinatorTaskRepository extends EntityRepository implements CoordinatorTaskRepository
{

    public function aCoordinatorTaskDetailForParticipant(string $participantId, string $id): CoordinatorTask
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('coordinatorTask');
        $qb->select('coordinatorTask')
                ->andWhere($qb->expr()->eq('coordinatorTask.id', ':id'))
                ->leftJoin('coordinatorTask.task', 'task')
                ->leftJoin('task.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('coordinator task not found');
        }
    }

    public function aCoordinatorTaskInProgram(string $programId, string $id): CoordinatorTask
    {
        $parameters = [
            'programId' => $programId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('coordinatorTask');
        $qb->select('coordinatorTask')
                ->andWhere($qb->expr()->eq('coordinatorTask.id', ':id'))
                ->leftJoin('coordinatorTask.task', 'task')
                ->leftJoin('task.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('coordinator task not found');
        }
    }

}
