<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\UserParticipantRepository,
    Domain\Model\Firm\Program\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineUserParticipantRepository extends EntityRepository implements UserParticipantRepository
{

    public function aProgramParticipationOfUser(string $userId, string $firmId, string $programId): UserParticipant
    {
        $params = [
            'userId' => $userId,
            'firmId' => $firmId,
            'programId' => $programId,
        ];
        
        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->leftJoin('programParticipation.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->leftJoin('programParticipation.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program participation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
        ];
        
        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->leftJoin('userParticipant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramParticipationsOfUser(string $userId, int $page, int $pageSize)
    {
        $params = [
            'userId' => $userId,
        ];
        
        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->leftJoin('programParticipation.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $userId): UserParticipant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'userId' => $userId,
        ];
        
        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->leftJoin('userParticipant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user participant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
