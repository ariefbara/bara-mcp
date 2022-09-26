<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Personnel\DedicatedMentor\DedicatedMentorRepository as DedicatedMentorRepository2;
use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineDedicatedMentorRepository extends DoctrineEntityRepository implements DedicatedMentorRepository, DedicatedMentorRepository2
{
    protected function setActiveOnlyFilter(QueryBuilder $qb, ?bool $cancelledStatus): void
    {
        if (isset($cancelledStatus)) {
            $qb->andWhere($qb->expr()->eq('dedicatedMentor.cancelled', ':cancelledStatus'))
                    ->setParameter('cancelledStatus', $cancelledStatus);
        }
    }

    public function aDedicatedMentorBelongsToConsultant(string $consultantId, string $dedicatedMentorId): DedicatedMentor
    {
        $params = [
            'consultantId' => $consultantId,
            'dedicatedMentorId' => $dedicatedMentorId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->andWhere($qb->expr()->eq('dedicatedMentor.id', ':dedicatedMentorId'))
                ->leftJoin('dedicatedMentor.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: dedicated mentor not found');
        }
    }

    public function aDedicatedMentorBelongsToParticipant(string $participantId, string $dedicatedMentorId): DedicatedMentor
    {
        $params = [
            'participantId' => $participantId,
            'dedicatedMentorId' => $dedicatedMentorId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->andWhere($qb->expr()->eq('dedicatedMentor.id', ':dedicatedMentorId'))
                ->leftJoin('dedicatedMentor.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: dedicated mentor not found');
        }
    }

    public function aDedicatedMentorInProgram(string $programId, string $dedicatedMentorId): DedicatedMentor
    {
        $params = [
            'programId' => $programId,
            'dedicatedMentorId' => $dedicatedMentorId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->andWhere($qb->expr()->eq('dedicatedMentor.id', ':dedicatedMentorId'))
                ->leftJoin('dedicatedMentor.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: dedicated mentor not found');
        }
    }

    public function allDedicatedMentorsBelongsToConsultant(string $consultantId, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        $params = [
            'consultantId' => $consultantId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->leftJoin('dedicatedMentor.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->setParameters($params);
        
        $this->setActiveOnlyFilter($qb, $cancelledStatus);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allDedicatedMentorsBelongsToParticipant(string $participantId, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        $params = [
            'participantId' => $participantId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->leftJoin('dedicatedMentor.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($params);
        
        $this->setActiveOnlyFilter($qb, $cancelledStatus);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allDedicatedMentorsOfParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        $params = [
            'programId' => $programId,
            'participantId' => $participantId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->leftJoin('dedicatedMentor.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        $this->setActiveOnlyFilter($qb, $cancelledStatus);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function allDedicatedMentorsOfConsultantInProgram(string $programId, string $consultantId, int $page,
            int $pageSize, ?bool $cancelledStatus)
    {
        $params = [
            'programId' => $programId,
            'consultantId' => $consultantId,
        ];
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->leftJoin('dedicatedMentor.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        $this->setActiveOnlyFilter($qb, $cancelledStatus);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aDedicatedMentorOfPersonnel(string $personnelId, string $dedicatedMentorId): DedicatedMentor
    {
        $params = [
            'personnelId' => $personnelId,
            'dedicatedMentorId' => $dedicatedMentorId,
        ];
        
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->andWhere($qb->expr()->eq('dedicatedMentor.id', ':dedicatedMentorId'))
                ->leftJoin('dedicatedMentor.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('dedicated mentor not found');
        }
    }

}
