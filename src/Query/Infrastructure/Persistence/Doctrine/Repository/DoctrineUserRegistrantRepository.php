<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\UserRegistrantRepository,
    Domain\Model\Firm\Program\UserRegistrant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineUserRegistrantRepository extends EntityRepository implements UserRegistrantRepository
{

    public function aProgramRegistrationOfUser(string $userId, string $programRegistrationId): UserRegistrant
    {
        $params = [
            'userId' => $userId,
            'programRegistrationId' => $programRegistrationId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
                ->leftJoin('programRegistration.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program registration not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('userRegistrant');
        $qb->select('userRegistrant')
                ->leftJoin('userRegistrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramRegistrationsOfUser(string $userId, int $page, int $pageSize)
    {
        $params = [
            'userId' => $userId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->leftJoin('programRegistration.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $userRegistrantId): UserRegistrant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'userRegistrantId' => $userRegistrantId,
        ];

        $qb = $this->createQueryBuilder('userRegistrant');
        $qb->select('userRegistrant')
                ->andWhere($qb->expr()->eq('userRegistrant.id', ':userRegistrantId'))
                ->leftJoin('userRegistrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user registration not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
