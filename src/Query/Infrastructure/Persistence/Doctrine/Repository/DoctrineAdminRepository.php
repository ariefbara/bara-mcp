<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Auth\AdminRepository as InterfaceForAuth,
    Application\Service\AdminRepository,
    Domain\Model\Admin
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineAdminRepository extends EntityRepository implements AdminRepository, InterfaceForAuth
{

    public function all(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('admin');
        $qb->select('admin')
                ->andWhere($qb->expr()->eq('admin.removed', 'false'));
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
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

}
