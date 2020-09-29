<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\ClientRepository,
    Domain\Model\Firm\Client,
    Domain\Service\Firm\ClientRepository as InterfaceForDomainService
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineClientRepository extends EntityRepository implements ClientRepository, InterfaceForDomainService
{
    
    public function all(string $firmId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
        ];
        
        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofEmail(string $firmIdentifier, string $email): Client
    {
        $params = [
            'firmIdentifier' => $firmIdentifier,
            'email' => $email,
        ];
        
        $qb = $this->createQueryBuilder('client');
        $qb->select('client')
                ->andWhere($qb->expr()->eq('client.email', ':email'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.identifier', ':firmIdentifier'))
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

    public function aClientHavingEmail(string $firmId, string $clientEmail): Client
    {
        $params = [
            "firmId" =>  $firmId,
            "clientEmail" =>  $clientEmail,
        ];
        
        $qb = $this->createQueryBuilder("client");
        $qb->select("client")
                ->andWhere($qb->expr()->eq("client.email", ":clientEmail"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
