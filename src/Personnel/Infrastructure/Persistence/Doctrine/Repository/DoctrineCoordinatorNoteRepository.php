<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorNoteRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineCoordinatorNoteRepository extends DoctrineEntityRepository implements CoordinatorNoteRepository
{
    
    public function add(CoordinatorNote $coordinatorNote): void
    {
        $this->persist($coordinatorNote);
    }

    public function ofId(string $id): CoordinatorNote
    {
        $params = [
            'id' => $id,
        ];
        $qb = $this->createQueryBuilder('coordinatorNote');
        $qb->select('coordinatorNote')
                ->andWhere($qb->expr()->eq('coordinatorNote.id', ':id'))
                ->leftJoin('coordinatorNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->setMaxResults(1)
                ->setParameters($params);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('coordinator note not found');
        }
    }

}
