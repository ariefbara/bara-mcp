<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\ClientRepository,
    Domain\Model\Client
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineClientRepository extends EntityRepository implements ClientRepository
{

    public function all(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('client');
        $qb->select('client');
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofEmail(string $email): Client
    {
        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->andWhere($qb->expr()->eq('client.email', ':email'))
                ->setParameter('email', $email)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $clientId): Client
    {
        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameter('clientId', $clientId)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
