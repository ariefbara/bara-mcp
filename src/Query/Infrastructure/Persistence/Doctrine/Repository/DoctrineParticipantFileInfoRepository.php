<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Client\ProgramParticipation\ParticipantFileInfoRepository,
    Domain\Model\Firm\Program\Participant\ParticipantFileInfo
};
use Resources\Exception\RegularException;

class DoctrineParticipantFileInfoRepository extends EntityRepository implements ParticipantFileInfoRepository
{

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantFileInfoId): ParticipantFileInfo
    {
        $params = [
            "participantFileInfoId" => $participantFileInfoId,
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];
        
        $qb = $this->createQueryBuilder('participantFileInfo');
        $qb->select('participantFileInfo')
                ->andWhere($qb->expr()->eq('participantFileInfo.removed', 'false'))
                ->andWhere($qb->expr()->eq('participantFileInfo.id', ':participantFileInfoId'))
                ->leftJoin('participantFileInfo.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: participant file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
