<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\FirmFileInfoRepository,
    Domain\Model\Firm\FirmFileInfo
};
use Resources\Exception\RegularException;

class DoctrineFirmFileInfoRepository extends EntityRepository implements FirmFileInfoRepository
{
    
    public function aFirmFileInfoBelongsToFirm(string $firmId, string $firmFileInfoId): FirmFileInfo
    {
        $params = [
            "firmId" => $firmId,
            "firmFileInfoId" => $firmFileInfoId,
        ];
        
        $qb = $this->createQueryBuilder("firmFileInfo");
        $qb->select("firmFileInfo")
                ->andWhere($qb->expr()->eq("firmFileInfo.id", ":firmFileInfoId"))
                ->leftJoin("firmFileInfo.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: firm file info not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
