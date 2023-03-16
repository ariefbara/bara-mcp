<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Task\Dependency\Firm\Program\Participant\LearningProgressRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineLearningProgressRepository extends DoctrineEntityRepository implements LearningProgressRepository
{

    public function aLearningProgressBelongsToParticipant(string $participantId, string $id)
    {
        $params = [
            'participantId' => $participantId,
            'id' => $id,
        ];
        $qb = $this->createQueryBuilder('learningProgress');
        $qb->select('learningProgress')
                ->andWhere($qb->expr()->eq('learningProgress.id', ':id'))
                ->leftJoin('learningProgress.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setMaxResults(1)
                ->setParameters($params);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('learning progress not found');
        }
    }

    public function learningProgressListBelongsToParticipant(string $participantId, int $page, int $pageSize)
    {
        $params = [
            'participantId' => $participantId,
        ];
        $qb = $this->createQueryBuilder('learningProgress');
        $qb->select('learningProgress')
                ->leftJoin('learningProgress.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
