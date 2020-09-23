<?php

namespace Team\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;
use Team\{
    Application\Service\ClientRepository,
    Domain\DependencyModel\Firm\Client
};

class DoctrineClientRepository extends EntityRepository implements ClientRepository
{

    public function ofId(string $firmId, string $clientId): Client
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder("client");
        $qb->select("client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
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
