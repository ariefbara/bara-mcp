<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use User\ {
    Application\Service\User\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\User\ProgramParticipation\ParticipantFileInfoRepository,
    Domain\Model\User\ProgramParticipation\ParticipantFileInfo,
    Domain\Service\ParticipantFileInfoRepository as InterfaceForDomainService
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

class DoctrineParticipantFileInfoRepository extends EntityRepository implements ParticipantFileInfoRepository,
        InterfaceForDomainService
{

    public function add(ParticipantFileInfo $participantFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($participantFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function fileInfoOf(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participationFileInfoId): FileInfo
    {
        $parameters = [
            "participantFileInfoId" => $participationFileInfoId,
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
            "userId" => $programParticipationCompositionId->getUserId(),
        ];
        
        $subQuery = $this->createQueryBuilder('participantFileInfo');
        $subQuery->select('tFileInfo.id')
                ->leftJoin('participantFileInfo.fileInfo', 'tFileInfo')
                ->andWhere($subQuery->expr()->eq('participantFileInfo.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('participantFileInfo.id', ':participantFileInfoId'))
                ->leftJoin('participantFileInfo.programParticipation', 'programParticipation')
                ->andWhere($subQuery->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.user', 'user')
                ->andWhere($subQuery->expr()->eq('user.id', ':userId'))
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
