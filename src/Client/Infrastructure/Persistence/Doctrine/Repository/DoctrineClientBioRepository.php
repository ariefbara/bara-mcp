<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ClientBioRepository;
use Client\Domain\Model\Client\ClientBio;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Resources\Exception\RegularException;

class DoctrineClientBioRepository extends EntityRepository implements ClientBioRepository
{
    
    public function aBioCorrespondWithForm(string $clientId, string $bioFormId): ClientBio
    {
        $params = [
            "clientId" => $clientId,
            "bioFormId" => $bioFormId,
        ];
        
        $qb = $this->createQueryBuilder("clientBio");
        $qb->select("clientBio")
                ->andWhere($qb->expr()->eq("clientBio.removed", "false"))
                ->leftJoin("clientBio.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("clientBio.bioForm", "bioForm")
                ->leftJoin("bioForm.form", "form")
                ->andWhere($qb->expr()->eq("form.id", ":bioFormId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client bio not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
