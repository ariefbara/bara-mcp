<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotFilter;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineMentoringSlotRepository extends EntityRepository implements MentoringSlotRepository
{
    protected function applyFilter(QueryBuilder $qb, MentoringSlotFilter $filter): void
    {
        if (!empty($filter->getConsultantId())) {
            $qb->andWhere($qb->expr()->eq('mentor.id', ':mentorId'))
                    ->setParameter('mentorId', $filter->getConsultantId());
        }
        
        if (!empty($filter->getFrom())) {
            $qb->andWhere($qb->expr()->gt('mentoringSlot.schedule.startTime', ':from'))
                    ->setParameter('from', $filter->getFrom());
        }
        
        if (!empty($filter->getTo())) {
            $qb->andWhere($qb->expr()->lt('mentoringSlot.schedule.endTime', ':to'))
                    ->setParameter('to', $filter->getTo());
        }
        
        if (!empty($filter->getConsultationSetupId())) {
            $qb->leftJoin('mentoringSlot.consultationSetup', 'consultationSetup')
                    ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                    ->setParameter('consultationSetupId', $filter->getConsultationSetupId());
        }
        
        $cancelledStatus = $filter->getCancelledStatus();
        if (isset($cancelledStatus)) {
            $qb->andWhere($qb->expr()->lt('mentoringSlot.cancelled', ':cancelledStatus'))
                    ->setParameter('cancelledStatus', $cancelledStatus);
        }
    }

    public function aMentoringSlotBelongsToPersonnel(string $personnelId, string $id): MentoringSlot
    {
        $params = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringSlot');
        $qb->select('mentoringSlot')
                ->andWhere($qb->expr()->eq('mentoringSlot.id', ':id'))
                ->leftJoin('mentoringSlot.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring slot not found');
        }
    }

    public function allMentoringSlotsBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringSlotFilter $filter)
    {
        $params = [
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('mentoringSlot');
        $qb->select('mentoringSlot')
                ->leftJoin('mentoringSlot.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params);
        
        $this->applyFilter($qb, $filter);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aMentoringSlotInProgram(string $programId, string $id): MentoringSlot
    {
        $params = [
            'programId' => $programId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringSlot');
        $qb->select('mentoringSlot')
                ->andWhere($qb->expr()->eq('mentoringSlot.id', ':id'))
                ->leftJoin('mentoringSlot.mentor', 'mentor')
                ->leftJoin('mentor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring slot not found');
        }
        
    }

    public function allMentoringSlotInProgram(string $programId, int $page, int $pageSize,
            MentoringSlotFilter $mentoringSlotFilter)
    {
        $params = [
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('mentoringSlot');
        $qb->select('mentoringSlot')
                ->leftJoin('mentoringSlot.mentor', 'mentor')
                ->leftJoin('mentor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        $this->applyFilter($qb, $filter);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
