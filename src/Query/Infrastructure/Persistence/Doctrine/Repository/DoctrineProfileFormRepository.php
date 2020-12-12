<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\ProfileFormRepository;
use Query\Domain\Model\Firm\ProfileForm;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineProfileFormRepository extends EntityRepository implements ProfileFormRepository
{
    
    public function aProfileFormInFirm(string $firmId, string $profileFormId): ProfileForm
    {
        $params = [
            "firmId" => $firmId,
            "profileFormId" => $profileFormId,
        ];
        
        $qb = $this->createQueryBuilder("profileForm");
        $qb->select("profileForm")
                ->andWhere($qb->expr()->eq("profileForm.id", ":profileFormId"))
                ->leftJoin("profileForm.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: profile form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProfileFormsInFirm(string $firmId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
        ];
        
        $qb = $this->createQueryBuilder("profileForm");
        $qb->select("profileForm")
                ->leftJoin("profileForm.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
