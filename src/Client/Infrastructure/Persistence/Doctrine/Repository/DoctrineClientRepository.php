<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\ClientRepository,
    Domain\Model\Client
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\Domain\Model\Firm;
use Resources\Exception\RegularException;

class DoctrineClientRepository extends EntityRepository implements ClientRepository
{

    public function ofEmail(string $firmIdentifier, string $email): Client
    {
        $params = [
            'firmIdentifier' => $firmIdentifier,
            'email' => $email,
        ];

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tFirm.id')
                ->from(Firm::class, 'tFirm')
                ->andWhere($subQuery->expr()->eq('tFirm.identifier', ':firmIdentifier'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->andWhere($qb->expr()->eq('client.email', ':email'))
                ->andWhere($qb->expr()->in('client.firmId', $subQuery->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $firmId, string $clientId): Client
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->andWhere($qb->expr()->in('client.firmId', ':firmId'))
                ->setParameters($params)
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
