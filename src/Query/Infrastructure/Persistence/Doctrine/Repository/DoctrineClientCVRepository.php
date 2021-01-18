<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Client\ClientCVRepository;
use Query\Domain\Model\Firm\Client\ClientCV;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineClientCVRepository extends EntityRepository implements ClientCVRepository
{

    public function aClientCVBelongsClientCorrespondWithClientCVForm(
            string $firmId, string $clientId, string $clientCVFormId): ClientCV
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "clientCVFormId" => $clientCVFormId,
        ];

        $qb = $this->createQueryBuilder("clientCV");
        $qb->select("clientCV")
                ->andWhere($qb->expr()->eq("clientCV.removed", "false"))
                ->leftJoin("clientCV.clientCVForm", "clientCVForm")
                ->andWhere($qb->expr()->eq("clientCVForm.id", ":clientCVFormId"))
                ->leftJoin("clientCV.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client cv not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allClientCVsBelongsClient(string $firmId, string $clientId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder("clientCV");
        $qb->select("clientCV")
                ->andWhere($qb->expr()->eq("clientCV.removed", "false"))
                ->leftJoin("clientCV.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
