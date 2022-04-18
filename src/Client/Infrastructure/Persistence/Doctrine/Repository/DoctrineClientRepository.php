<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\ClientRepository;
use Client\Domain\Model\Client;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;
use Resources\Uuid;

class DoctrineClientRepository extends DoctrineEntityRepository implements ClientRepository
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

    public function add(Client $client): void
    {
        $em = $this->getEntityManager();
        $em->persist($client);
        $em->flush();
    }

    public function containRecordWithEmail(string $firmIdentifier, string $email): bool
    {
        $params = [
            'firmIdentifier' => $firmIdentifier,
            'email' => $email,
        ];
        $firmQb = $this->getEntityManager()->createQueryBuilder();
        $firmQb->select('firm.id')
                ->from(Firm::class, 'firm')
                ->andWhere($firmQb->expr()->eq('firm.identifier', ':firmIdentifier'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('client');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('client.email', ':email'))
                ->andWhere($qb->expr()->in('client.firmId', $firmQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function aClientOfId(string $id): Client
    {
        return $this->findOneByIdOrDie($id, 'client');
    }

}
