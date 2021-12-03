<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineDeclaredMentoringRepository extends DoctrineEntityRepository implements DeclaredMentoringRepository
{
    
    public function aDeclaredMentoringBelongsToPersonnel(string $personnelId, string $id): DeclaredMentoring
    {
        $parameters = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];
        
        $qb = $this->createQueryBuilder('declaredMentoring');
        $qb->select('declaredMentoring')
                ->andWhere($qb->expr()->eq('declaredMentoring.id', ':id'))
                ->leftJoin('declaredMentoring.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: declared mentoring not found');
        }
    }

    public function aDeclaredMentoringBelongsToParticipant(string $participantId, string $id): DeclaredMentoring
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];
        
        $qb = $this->createQueryBuilder('declaredMentoring');
        $qb->select('declaredMentoring')
                ->andWhere($qb->expr()->eq('declaredMentoring.id', ':id'))
                ->leftJoin('declaredMentoring.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: declared mentoring not found');
        }
    }

}
