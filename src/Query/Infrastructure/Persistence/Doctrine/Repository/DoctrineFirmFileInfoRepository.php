<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\FirmFileInfoRepository;
use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Task\Dependency\Firm\FirmFileInfoRepository as FirmFileInfoRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineFirmFileInfoRepository extends DoctrineEntityRepository implements FirmFileInfoRepository, FirmFileInfoRepository2
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

    public function aFirmFileInfoInFirm(string $firmId, string $id): FirmFileInfo
    {
        $params = [
            "firmId" => $firmId,
            "firmFileInfoId" => $id,
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

    public function ofId(string $id): FirmFileInfo
    {
        return $this->findOneByIdOrDie($id, 'firm file info');
    }

}
