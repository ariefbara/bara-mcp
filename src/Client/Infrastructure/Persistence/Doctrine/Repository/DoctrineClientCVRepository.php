<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ClientCVRepository;
use Client\Domain\Model\Client\ClientCV;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Resources\Exception\RegularException;

class DoctrineClientCVRepository extends EntityRepository implements ClientCVRepository
{
    
    public function aClientCVCorrespondWithCVForm(string $clientId, string $clientCVFormId): ClientCV
    {
        $params = [
            "clientId" => $clientId,
            "clientCVFormId" => $clientCVFormId,
        ];
        
        $qb = $this->createQueryBuilder("clientCV");
        $qb->select("clientCV")
                ->andWhere($qb->expr()->eq("clientCV.removed", "false"))
                ->leftJoin("clientCV.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("clientCV.clientCVForm", "clientCVForm")
                ->andWhere($qb->expr()->eq("clientCVForm.id", ":clientCVFormId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client cv not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
