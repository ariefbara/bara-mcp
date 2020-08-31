<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\ClientRepository,
    Domain\Model\Firm\Client
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

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function containRecordWithEmail(string $firmIdentifier, string $clientEmail): bool
    {
        $params = [
            'firmIdentifier' => $firmIdentifier,
            'clientEmail' => $clientEmail,
        ];
        
        $qb = $this->createQueryBuilder('client');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('client.email', ':clientEmail'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.identifier', ':firmIdentifier'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

    public function ofId(string $firmId, string $clientId): Client
    {
        $params = [
            'clientId' => $clientId,
            'firmId' => $firmId,
        ];
        
        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client not found';
            throw RegularException::notFound($errorDetail);
        }
                
    }

}
