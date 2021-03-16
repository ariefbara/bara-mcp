<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Client\ClientBioRepository;
use Query\Domain\Model\Firm\Client\ClientBio;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineClientBioRepository extends EntityRepository implements ClientBioRepository
{

    public function aBioBelongsToClientCorrespondWithBioForm(
            string $firmId, string $clientId, string $bioFormId): ClientBio
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "bioFormId" => $bioFormId,
        ];

        $qb = $this->createQueryBuilder("clientBio");
        $qb->select("clientBio")
                ->andWhere($qb->expr()->eq("clientBio.removed", "false"))
                ->leftJoin("clientBio.bioForm", "bioForm")
                ->leftJoin("bioForm.form", "form")
                ->andWhere($qb->expr()->eq("form.id", ":bioFormId"))
                ->leftJoin("clientBio.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client bio not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allBiosBelongsClient(string $firmId, string $clientId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder("clientBio");
        $qb->select("clientBio")
                ->andWhere($qb->expr()->eq("clientBio.removed", "false"))
                ->leftJoin("clientBio.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
