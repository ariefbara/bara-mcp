<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\Client\ClientRegistrantRepository;
use Participant\Domain\Model\ClientRegistrant;
use Resources\Exception\RegularException;

class DoctrineClientRegistrantRepository extends EntityRepository implements ClientRegistrantRepository
{
    
    public function aClientRegistrant(string $firmId, string $clientId, string $programRegistrationId): ClientRegistrant
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "clientRegistrantId" => $programRegistrationId,
        ];
        
        $qb = $this->createQueryBuilder("clientRegistrant");
        $qb->select("clientRegistrant")
                ->andWhere($qb->expr()->eq("clientRegistrant.id", ":clientRegistrantId"))
                ->leftJoin("clientRegistrant.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program registration not found";
            throw RegularException::notFound($errorDetail);
        }
                
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
