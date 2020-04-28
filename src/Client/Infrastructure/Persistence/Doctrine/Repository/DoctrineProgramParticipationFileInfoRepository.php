<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\ProgramParticipationFileInfoRepository,
    Domain\Model\Client\ProgramParticipation\ProgramParticipationFileInfo,
    Domain\Service\ProgramParticipationFileInfoRepository as InterfaceForDomainService
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};
use Shared\Domain\Model\FileInfo;

class DoctrineProgramParticipationFileInfoRepository extends EntityRepository implements ProgramParticipationFileInfoRepository,
        InterfaceForDomainService
{

    public function add(ProgramParticipationFileInfo $programParticipationFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($programParticipationFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function fileInfoOf(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $programParticipationFileInfoId): FileInfo
    {
        $parameters = [
            "programParticipationFileInfoId" => $programParticipationFileInfoId,
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];
        
        $subQuery = $this->createQueryBuilder('programParticipationFileInfo');
        $subQuery->select('tFileInfo.id')
                ->leftJoin('programParticipationFileInfo.fileInfo', 'tFileInfo')
                ->andWhere($subQuery->expr()->eq('programParticipationFileInfo.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('programParticipationFileInfo.id', ':programParticipationFileInfoId'))
                ->leftJoin('programParticipationFileInfo.programParticipation', 'programParticipation')
                ->andWhere($subQuery->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($subQuery->expr()->eq('client.id', ':clientId'))
                ->setMaxResults(1);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fileInfo')
                ->from(FileInfo::class, 'fileInfo')
                ->andWhere($qb->expr()->in('fileInfo.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: file info not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
