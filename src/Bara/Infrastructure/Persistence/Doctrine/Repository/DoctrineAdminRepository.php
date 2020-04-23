<?php

namespace Bara\Infrastructure\Persistence\Doctrine\Repository;

use Bara\ {
    Application\Auth\AdminRepository as InterfaceForAuthorization,
    Application\Service\AdminRepository,
    Domain\Model\Admin
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder,
    Uuid
};


class DoctrineAdminRepository extends EntityRepository implements AdminRepository, InterfaceForAuthorization
{
    
    public function add(Admin $admin): void
    {
        $em = $this->getEntityManager();
        $em->persist($admin);
        $em->flush();
    }

    public function isEmailAvailable(string $email): bool
    {
        $qb = $this->createQueryBuilder('admin');
        $qb->select('1')
            ->andWhere($qb->expr()->eq('admin.email', ':email'))
            ->andWhere($qb->expr()->eq('admin.removed', 'false'))
            ->setParameter('email', $email)
            ->setMaxResults(1);
        return empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();

    }

    public function ofId(string $adminId): Admin
    {
        $qb = $this->createQueryBuilder('admin');
        $qb->select('admin')
            ->andWhere($qb->expr()->eq('admin.id', ':id'))
            ->andWhere($qb->expr()->eq('admin.removed', 'false'))
            ->setParameter('id', $adminId)
            ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: sys admin not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function all(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('admin');
        $qb->select('admin')
            ->andWhere($qb->expr()->eq('admin.removed', 'false'));
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofEmail(string $email): Admin
    {
        $qb = $this->createQueryBuilder('admin');
        $qb->select('admin')
            ->andWhere($qb->expr()->eq('admin.email', ':email'))
            ->andWhere($qb->expr()->eq('admin.removed', 'false'))
            ->setParameter('email', $email)
            ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: admin not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfId(string $adminId): bool
    {
        $qb = $this->createQueryBuilder('admin');
        $qb->select('1')
            ->andWhere($qb->expr()->eq('admin.id', ':id'))
            ->andWhere($qb->expr()->eq('admin.removed', 'false'))
            ->setParameter('id', $adminId)
            ->setMaxResults(1);
        return !empty($qb->getQuery()->getResult());
    }

}
