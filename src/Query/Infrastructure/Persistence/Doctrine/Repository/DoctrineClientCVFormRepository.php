<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\ClientCVFormRepository;
use Query\Domain\Model\Firm\ClientCVForm;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineClientCVFormRepository extends EntityRepository implements ClientCVFormRepository
{
    
    public function aClientCVFormInFirm(string $firmId, string $clientCVFormId): ClientCVForm
    {
        $params = [
            "firmId" => $firmId,
            "clientCVFormId" => $clientCVFormId,
        ];
        
        $qb = $this->createQueryBuilder("clientCVForm");
        $qb->select("clientCVForm")
                ->andWhere($qb->expr()->eq("clientCVForm.id", ":clientCVFormId"))
                ->leftJoin("clientCVForm.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client cv form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allClientCVFormsInFirm(string $firmId, int $page, int $pageSize, ?bool $disableStatus)
    {
        $params = [
            "firmId" => $firmId,
        ];
        
        $qb = $this->createQueryBuilder("clientCVForm");
        $qb->select("clientCVForm")
                ->leftJoin("clientCVForm.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        if (isset($disableStatus)) {
                $qb->andWhere($qb->expr()->eq("clientCVForm.disabled", ":disableStatus"))
                        ->setParameter("disableStatus", $disableStatus);
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
