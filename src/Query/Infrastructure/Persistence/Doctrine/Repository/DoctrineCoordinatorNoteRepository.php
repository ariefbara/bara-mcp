<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNote;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteFilter;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineCoordinatorNoteRepository extends EntityRepository implements CoordinatorNoteRepository
{

    public function aCoordinatorNoteBelongsToPersonnel(string $personnelId, string $id): CoordinatorNote
    {
        $parameters = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('coordinatorNote');
        $qb->select('coordinatorNote')
                ->andWhere($qb->expr()->eq('coordinatorNote.id', ':id'))
                ->leftJoin('coordinatorNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->leftJoin('coordinatorNote.coordinator', 'coordinator')
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setMaxResults(1)
                ->setParameters($parameters);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('coordinator note not found');
        }
    }

    public function allCoordinatorNotesBelongsToPersonnel(string $personnelId, CoordinatorNoteFilter $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('coordinatorNote');
        $qb->select('coordinatorNote')
                ->leftJoin('coordinatorNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->leftJoin('coordinatorNote.coordinator', 'coordinator')
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($parameters);

        $spesificCoordinatorId = $filter->getCoordinatorId();
        if (isset($spesificCoordinatorId)) {
            $qb->andWhere($qb->expr()->eq('coordinator.id', ':coordinatorId'))
                    ->setParameter('coordinatorId', $spesificCoordinatorId);
        }

        $modifiedTimeOrder = $filter->getNoteFilter()->getModifiedTimeOrder();
        if (isset($modifiedTimeOrder)) {
            $qb->addOrderBy('note.modifiedTime', $modifiedTimeOrder->getOrder());
        }

        $createdTimeOrder = $filter->getNoteFilter()->getCreatedTimeOrder();
        if (isset($createdTimeOrder)) {
            $qb->addOrderBy('note.createdTime', $createdTimeOrder->getOrder());
        }
        
        $page = $filter->getPaginationFilter()->getPage();
        $pageSize = $filter->getPaginationFilter()->getPageSize();
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aCoordinatorNoteAccessibleByParticipant(string $participantId, string $id): CoordinatorNote
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];
        
        $qb = $this->createQueryBuilder('coordinatorNote');
        $qb->select('coordinatorNote')
                ->andWhere($qb->expr()->eq('coordinatorNote.id', ':id'))
                ->leftJoin('coordinatorNote.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('coordinatorNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('coordinator note not found');
        }
    }

}
