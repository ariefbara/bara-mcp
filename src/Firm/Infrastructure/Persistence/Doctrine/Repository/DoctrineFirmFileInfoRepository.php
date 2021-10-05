<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\FirmFileInfoRepository;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository as FirmFileInfoRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;
use Resources\Uuid;

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

    public function add(FirmFileInfo $firmFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($firmFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $id): FirmFileInfo
    {
        return $this->findOneByIdOrDie($id, 'firm file info');
    }

}
