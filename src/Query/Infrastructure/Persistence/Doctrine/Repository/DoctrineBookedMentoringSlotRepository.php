<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlot;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotFilter;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotRepository;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineBookedMentoringSlotRepository extends EntityRepository implements BookedMentoringSlotRepository
{

    public function aBookedMentoringSlotBelongsToPersonnel(string $personnelId, string $id): BookedMentoringSlot
    {
        $params = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('bookedMentoringSlot');
        $qb->select('bookedMentoringSlot')
                ->andWhere($qb->expr()->eq('bookedMentoringSlot.id', ':id'))
                ->leftJoin('bookedMentoringSlot.mentoringSlot', 'mentoringSlot')
                ->leftJoin('mentoringSlot.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params)
                ->setMaxResults(1);
    }

    public function allBookedMentoringSlotsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, BookedMentoringSlotFilter $filter)
    {
        $params = [
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('bookedMentoringSlot');
        $qb->select('bookedMentoringSlot')
                ->leftJoin('bookedMentoringSlot.mentoringSlot', 'mentoringSlot')
                ->leftJoin('mentoringSlot.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params);

        if (!empty($filter->getConsultantId())) {
            $qb->andWhere($qb->expr()->eq('mentor.id', ':consultantId'))
                    ->setParameter('consultantId', $filter->getConsultantId());
        }

        if (!empty($filter->getMentoringSlotId())) {
            $qb->andWhere($qb->expr()->eq('mentoringSlot.id', ':mentoringSlotId'))
                    ->setParameter('mentoringSlotId', $filter->getMentoringSlotId());
        }

        $cancelledStatus = $filter->getCancelledStatus();
        if (isset($cancelledStatus)) {
            $qb->andWhere($qb->expr()->eq('bookedMentoringSlot.cancelled', ':cancelledStatus'))
                    ->setParameter('cancelledStatus', $cancelledStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
