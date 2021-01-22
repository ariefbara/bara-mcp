<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\BioFormRepository;
use Query\Domain\Model\Firm\BioForm;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineBioFormRepository extends EntityRepository implements BioFormRepository
{
    
    public function aBioFormInFirm(string $firmId, string $bioFormId): BioForm
    {
        $params = [
            "firmId" => $firmId,
            "bioFormId" => $bioFormId,
        ];
        
        $qb = $this->createQueryBuilder("bioForm");
        $qb->select("bioForm")
                ->leftJoin("bioForm.form", "form")
                ->andWhere($qb->expr()->eq("form.id", ":bioFormId"))
                ->leftJoin("bioForm.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: bio form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allBioFormsInFirm(string $firmId, int $page, int $pageSize, ?bool $disableStatus)
    {
        $params = [
            "firmId" => $firmId,
        ];
        
        $qb = $this->createQueryBuilder("bioForm");
        $qb->select("bioForm")
                ->leftJoin("bioForm.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        if (isset($disableStatus)) {
                $qb->andWhere($qb->expr()->eq("bioForm.disabled", ":disableStatus"))
                        ->setParameter("disableStatus", $disableStatus);
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
