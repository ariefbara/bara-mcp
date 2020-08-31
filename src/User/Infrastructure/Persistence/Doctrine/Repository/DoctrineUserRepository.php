<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};
use User\ {
    Application\Service\UserRepository,
    Domain\Model\User
};

class DoctrineUserRepository extends EntityRepository implements UserRepository
{

    public function add(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    public function containRecordWithEmail(string $email): bool
    {
        $params = [
            'userEmail' => $email,
        ];

        $qb = $this->createQueryBuilder('user');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('user.email', ':userEmail'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofEmail(string $email): User
    {
        $params = [
            'userEmail' => $email,
        ];

        $qb = $this->createQueryBuilder('user');
        $qb->select('user')
                ->andWhere($qb->expr()->eq('user.email', ':userEmail'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $userId): User
    {
        $params = [
            'userId' => $userId,
        ];

        $qb = $this->createQueryBuilder('user');
        $qb->select('user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
