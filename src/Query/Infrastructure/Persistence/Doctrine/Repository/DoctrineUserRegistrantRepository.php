<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\User\ProgramRegistrationRepository,
    Domain\Model\User\UserRegistrant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineUserRegistrantRepository extends EntityRepository implements ProgramRegistrationRepository
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

}
