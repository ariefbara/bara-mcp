<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\ClientRepository,
    Domain\Model\Client
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineClientRepository extends EntityRepository implements ClientRepository
{

    public function add(Client $client): void
    {
        $em = $this->getEntityManager();
        $em->persist($client);
        $em->flush();
    }

    public function containRecordWithEmail(string $email): bool
    {
        $qb = $this->createQueryBuilder('client');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('client.email', ':email'))
                ->setParameter('email', $email)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
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

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
