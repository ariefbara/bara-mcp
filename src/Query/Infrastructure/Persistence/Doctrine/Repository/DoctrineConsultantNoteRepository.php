<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Personnel\Consultant\ConsultantNote;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteFilter;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineConsultantNoteRepository extends EntityRepository implements ConsultantNoteRepository
{

    public function aConsultantNoteBelongsToPersonnel(string $personnelId, string $id): ConsultantNote
    {
        $parameters = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('consultantNote');
        $qb->select('consultantNote')
                ->andWhere($qb->expr()->eq('consultantNote.id', ':id'))
                ->leftJoin('consultantNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->leftJoin('consultantNote.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setMaxResults(1)
                ->setParameters($parameters);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('consultant note not found');
        }
    }

    public function allConsultantNotesBelongsToPersonnel(string $personnelId, ConsultantNoteFilter $filter)
    {
        $parameters = [
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('consultantNote');
        $qb->select('consultantNote')
                ->leftJoin('consultantNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->leftJoin('consultantNote.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($parameters);

        $spesificConsultantId = $filter->getConsultantId();
        if (isset($spesificConsultantId)) {
            $qb->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                    ->setParameter('consultantId', $spesificConsultantId);
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

}
